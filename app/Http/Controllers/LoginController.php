<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Events\Verified;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Cookie;
use App\Mail\NewPasswordMail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Models\Campaign;
use App\Models\Donation;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;
use App\Models\EventRegistration;
use App\Models\SiteSetting;

class LoginController
{


    protected function noCacheView($view, $data = [])
    {
        $response = response()->view($view, $data);

        return $response->header('Cache-Control', 'no-cache, no-store, must-revalidate')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }

    //   ===============================================LOGIN CONTROLLER PART==========================================
    public function landingpage()
    {
        $homepageStats = [
            'total_donations' => $this->formatCompactNumber(
                Campaign::get(['current_amount'])->sum(fn ($campaign) => (float) $campaign->current_amount)
            ),
            'active_volunteers' => $this->formatCompactNumber(EventRegistration::count()),
            'successful_campaigns' => $this->formatCompactNumber(
                Campaign::whereIn('status', ['ended', 'completed'])->count()
            ),
        ];

        $featuredCampaigns = Campaign::where('status', 'active')
            ->orderByDesc('views')
            ->orderByDesc('current_amount')
            ->orderByDesc('donor_count')
            ->latest()
            ->take(3)
            ->get();

        // Compute donations_count in PHP for MongoDB compatibility
        $featuredCampaigns->each(function ($campaign) {
            $campaign->donations_count = Donation::where('campaign_id', $campaign->campaign_id)->count();
        });

        return $this->noCacheView('landpage', compact('homepageStats', 'featuredCampaigns'));
    }

    private function formatCompactNumber($value): string
    {
        $value = (float) $value;

        if ($value >= 1000000) {
            return number_format($value / 1000000, 1) . 'M';
        }

        if ($value >= 1000) {
            return number_format($value / 1000, 1) . 'K';
        }

        return number_format($value);
    }
    public function loginpage()
    {
        if (Auth::check()) {
            return redirect()->route('landpage');
        }
        return response()
            ->view('login.loginpage')
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache')
            ->header('Expires', 'Sat, 01 Jan 2000 00:00:00 GMT');
    }


    public function loginaccount(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            $user = Auth::user();

            // Block suspended accounts
            if (($user->status ?? 'active') === 'suspended') {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return back()->with('error', 'This account has been suspended. Please contact support.');
            }

            // If not verified, keep them logged in but force them to the notice page
            if (!$user->hasVerifiedEmail()) {
                return redirect()
                    ->route('verification.notice')
                    ->with('message', 'Please verify your email. We can resend the link.');
            }


            return redirect()->route('landpage');
        }

        return back()->with('error', 'Invalid email or password.');
    }


    public function redirect()
    {
        if (!SiteSetting::isTrue('user.google_login.enabled')) {
            return redirect()->route('login.page')
                ->with('error', 'Google sign-in is currently disabled.');
        }

        return Socialite::driver('google')->redirect();
    }

    public function callbackGoogle()
    {
        if (!SiteSetting::isTrue('user.google_login.enabled')) {
            return redirect()->route('login.page')
                ->with('error', 'Google sign-in is currently disabled.');
        }
        try {
            //  Get Google user data
            $googleUser = Socialite::driver('google')->user();

            // Look up by google_id first
            $user = User::where('google_id', $googleUser->getId())->first();

            // If not found, look up by email
            if (!$user && $googleUser->getEmail()) {
                $user = User::where('email', $googleUser->getEmail())->first();
            }

            // create new user
            if (!$user) {
                $user = User::create([
                    'first_name'       => $googleUser->user['given_name'] ?? $googleUser->getName(),
                    'last_name'        => $googleUser->user['family_name'] ?? '',
                    'email'             => $googleUser->getEmail(),
                    'google_id'         => $googleUser->getId(),
                    'password'          => null, // password nullable
                    'email_verified_at' => ($googleUser->user['verified_email'] ?? false) ? now() : null,
                    'profile_photo_url' => $googleUser->getAvatar(),
                ]);
            } else {

                // Only set profile_photo_url from Google if we don't already have one
                if (!$user->profile_photo_url && $googleUser->getAvatar()) {
                    $user->profile_photo_url = $googleUser->getAvatar();
                }

                // mark it verified
                if (($googleUser->user['verified_email'] ?? false) && !$user->email_verified_at) {
                    $user->email_verified_at = now();
                }

                //
                if (
                    (!$user->profile_photo_url) ||
                    str_contains($user->profile_photo_url, 'googleusercontent.com')
                ) {
                    if ($googleUser->getAvatar()) {
                        $user->profile_photo_url = $googleUser->getAvatar();
                    }
                }
                //


                $user->save();
            }

            // Log the user in
            if (($user->status ?? 'active') === 'suspended') {
                return redirect()->route('login.page')
                    ->with('error', 'This account has been suspended. Please contact support.');
            }

            Auth::login($user);
            request()->session()->regenerate();
            return redirect()->route('landpage');
        } catch (\Throwable $th) {
            return redirect()->route('login.page')
                ->withErrors(['google' => 'Google login failed: ' . $th->getMessage()]);
        }
    }

    // FORGOT PASSWORD
    public function sendNewPassword(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['error' => 'Email not found!'], 404);
        }

        // Generate a reset token
        $token = Str::random(64);


        DB::table('password_resets')->updateOrInsert(
            ['email' => $user->email],
            [
                'token' => Hash::make($token),
                'created_at' => Carbon::now(),
            ]
        );

        // Generate reset URL
        $resetUrl = url('/reset-password?token=' . $token . '&email=' . urlencode($user->email));

        // Send email
        Mail::to($user->email)->send(new NewPasswordMail($user, $resetUrl));

        return response()->json([
            'success' => 'We’ve emailed you a password reset link! Please check your inbox (or spam folder).'
        ]);
    }

    public function showResetForm(Request $request)
    {
        return view('auth.reset-password', [
            'token' => $request->query('token'),
            'email' => $request->query('email'),
        ]);
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
            'token' => 'required'
        ]);

        $record = DB::table('password_resets')->where('email', $request->email)->first();

        if (!$record || !Hash::check($request->token, $record->token)) {
            return back()->withErrors(['email' => 'Invalid or expired reset token.']);
        }

        if (Carbon::parse($record->created_at)->addMinutes(5)->isPast()) {
            DB::table('password_resets')->where('email', $request->email)->delete();
            return back()->withErrors(['email' => 'Reset link has expired. Please request a new one.']);
        }

        // Update password
        User::where('email', $request->email)->update([
            'password' => Hash::make($request->password)
        ]);

        // Delete token
        DB::table('password_resets')->where('email', $request->email)->delete();


        return back()->with('success', 'Your password has been reset successfully!');
    }

    //   ===================================================================================================================


    //   ===============================================REGISTER CONTROLLER PART==========================================
    public function registerpage()
    {
        if (!SiteSetting::isTrue('user.registration.enabled')) {
            return redirect()->route('login.page')
                ->with('error', 'New account registration is currently disabled.');
        }

        return view('login.registerpage');
    }

    public function checkEmail(Request $request)
    {
        $exists = User::where('email', $request->email)->exists();
        return response()->json(['exists' => $exists]);
    }

    public function checkPhone(Request $request)
    {
        $phone = $request->query('phone'); // get phone from query string
        $exists = User::where('phone_number', $phone)->exists();
        return response()->json(['exists' => $exists]);
    }

    public function registeraccount(Request $request)
    {
        if (!SiteSetting::isTrue('user.registration.enabled')) {
            return back()->with('error', 'New account registration is currently disabled.');
        }

        $request->validate([
            'first_name'   => 'required|string|max:100',
            'last_name'    => 'required|string|max:100',
            'email'        => 'required|email|unique:user_account,email',
            'phone_number' => 'required|string|max:20|unique:user_account,phone_number',
            'birthday' => 'required|date|before:today',
            'password'     => 'required|min:8|max:20',
        ]);

        // Create user
        $user = User::create([
            'first_name'   => $request->first_name,
            'last_name'    => $request->last_name,
            'email'        => $request->email,
            'phone_number' => $request->phone_number,
            'birthday' => $request->birthday,
            'password'     => bcrypt($request->password),
            'status'       => 'unverified',
        ]);

        // Log the user in temporarily to access verification page
        Auth::login($user);

        try {
            event(new Registered($user));
        } catch (\Throwable $e) {
            Log::error('Email verification send failed after registration.', [
                'user_id' => $user->getKey(),
                'email' => $user->email,
                'error' => $e->getMessage(),
            ]);

            return redirect()
                ->route('verification.notice')
                ->with('mail_error', 'We could not send the verification email. Please check the mail settings and try Resend Verification Email.');
        }

        // Redirect to verification notice page instead of login
        return redirect()
            ->route('verification.notice')
            ->with('message', 'Verification email sent. Please check your inbox or spam folder.');
    }

    //VERIFCATION NOTICE
    public function verificationNotice()
    {
        return view('auth.verify-email');
    }

    //VERIFY EMAIL
    public function verifyEmail(Request $request)
    {
        $user = User::findOrFail($request->route('id'));


        if (! hash_equals(
            (string) $request->route('hash'),
            sha1($user->getEmailForVerification())
        )) {
            abort(403, 'Invalid verification link.');
        }

        if (! $user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
            event(new Verified($user));
        }


        return view('auth.email-verified');
    }

    public function checkVerificationStatus(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['verified' => false, 'status' => '']);
        }

        $user = Auth::user();
        $status = strtolower(optional($user->identityStatus)->status ?? '');

        return response()->json([
            'verified' => $user->hasVerifiedEmail(),
            'status' => $status,
            'updated_at' => optional($user->identityStatus)->updated_at,
        ]);
    }

    //VERICATION RESEND
    public function resendVerification(Request $request)
    {
        try {
            $request->user()->sendEmailVerificationNotification();
        } catch (\Throwable $e) {
            Log::error('Email verification resend failed.', [
                'user_id' => $request->user()?->getKey(),
                'email' => $request->user()?->email,
                'error' => $e->getMessage(),
            ]);

            return back()->with('mail_error', 'Verification email was not sent because the mail server rejected the current credentials.');
        }

        return back()->with('message', 'Verification link sent!');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('landpage');
    }
    //   =================================================================================================================
}
