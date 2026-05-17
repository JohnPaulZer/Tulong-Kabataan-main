<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Cookie;
use App\Mail\NewPasswordMail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;
use App\Models\Campaign;
use App\Models\Donation;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;
use Illuminate\Validation\ValidationException;
use App\Models\EventRegistration;
use App\Models\SiteSetting;
use App\Models\VerificationRequest;
use App\Services\Auth\EmailVerificationTokenService;

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
            ->orderByDesc('current_amount')
            ->orderByDesc('donor_count')
            ->orderByDesc('views')
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
            Log::warning('Google login failed safely.', [
                'error' => $th::class,
            ]);

            return redirect()->route('login.page')
                ->withErrors(['google' => 'Google login failed. Please try again or use email and password.']);
        }
    }

    // FORGOT PASSWORD
    public function sendNewPassword(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'success' => 'If that email exists, we sent a password reset link. Please check your inbox or spam folder.'
            ]);
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
            'success' => 'If that email exists, we sent a password reset link. Please check your inbox or spam folder.'
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
        $email = Str::lower(trim((string) $request->query('email')));
        if ($email === '' || ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return response()->json(['exists' => false]);
        }

        $exists = User::where('email', $email)->exists();
        return response()->json(['exists' => $exists]);
    }

    public function checkPhone(Request $request)
    {
        $phone = preg_replace('/\D+/', '', (string) $request->query('phone')); // get phone from query string
        if (! preg_match('/^09\d{9}$/', $phone)) {
            return response()->json(['exists' => false]);
        }

        $exists = User::where('phone_number', $phone)->exists();
        return response()->json(['exists' => $exists]);
    }

    public function registeraccount(Request $request)
    {
        if (!SiteSetting::isTrue('user.registration.enabled')) {
            if ($request->expectsJson()) {
                return $this->jsonAuthResponse(
                    'registration_closed',
                    'New account registration is currently disabled.',
                    ['redirect' => route('login.page')],
                    403
                );
            }

            return back()->with('error', 'New account registration is currently disabled.');
        }

        $request->merge([
            'first_name' => trim((string) $request->input('first_name')),
            'last_name' => trim((string) $request->input('last_name')),
            'email' => Str::lower(trim((string) $request->input('email'))),
            'phone_number' => preg_replace('/\D+/', '', (string) $request->input('phone_number')),
        ]);

        $request->validate([
            'first_name'   => 'required|string|max:100',
            'last_name'    => 'required|string|max:100',
            'email'        => 'required|email:rfc|max:255',
            'phone_number' => 'required|regex:/^09\d{9}$/',
            'birthday' => 'required|date|before_or_equal:' . now()->subYears(18)->toDateString(),
            'password'     => 'required|min:8|max:20',
        ], [
            'birthday.before_or_equal' => 'You must be at least 18 years old.',
            'phone_number.regex' => 'Enter an 11-digit phone number starting with 09.',
        ]);

        $lock = Cache::lock('registration:' . hash('sha256', (string) $request->email), 15);
        $lockAcquired = false;

        try {
            if (! $lock->get()) {
                throw ValidationException::withMessages([
                    'email' => 'Registration is already processing for this email. Please wait a moment.',
                ]);
            }
            $lockAcquired = true;

            if (User::where('email', $request->email)->exists()) {
                throw ValidationException::withMessages([
                    'email' => 'This email is already registered. Please log in or use the verification resend option.',
                ]);
            }

            if (User::where('phone_number', $request->phone_number)->exists()) {
                throw ValidationException::withMessages([
                    'phone_number' => 'This phone number is already registered.',
                ]);
            }

            $user = User::create([
                'first_name'   => $request->first_name,
                'last_name'    => $request->last_name,
                'email'        => $request->email,
                'phone_number' => $request->phone_number,
                'birthday' => $request->birthday,
                'password'     => $request->password,
                'status'       => 'unverified',
            ]);

            Auth::login($user);
            $request->session()->regenerate();
        } finally {
            if ($lockAcquired) {
                $lock->release();
            }
        }

        $message = 'Account created. Please continue to the verification page to send your email verification link.';
        session()->flash('message', $message);

        if ($request->expectsJson()) {
            return $this->jsonAuthResponse(
                'verification_pending',
                $message,
                [
                    'redirect' => route('verification.notice'),
                    'email_status' => 'not_sent',
                ],
                201
            );
        }

        return redirect()
            ->route('verification.notice');
    }

    //VERIFCATION NOTICE
    public function verificationNotice()
    {
        if (Auth::user()?->hasVerifiedEmail()) {
            return redirect()->route('landpage')
                ->with('message', 'Your email is already verified.');
        }

        return view('auth.verify-email');
    }

    //VERIFY EMAIL
    public function verifyEmail(Request $request, EmailVerificationTokenService $tokens)
    {
        $result = $tokens->verify(
            (string) $request->route('id'),
            (string) $request->route('token')
        );

        $user = $result['user'] ?? null;

        if ($result['status'] === 'success' && $user instanceof User) {
            event(new Verified($user));

            if (($user->status ?? 'active') === 'suspended') {
                return redirect()->route('login.page')
                    ->with('error', 'This account has been suspended. Please contact support.');
            }

            Auth::login($user);
            $request->session()->regenerate();

            return redirect()->route('landpage')
                ->with('message', 'Your email has been verified. Welcome to Tulong Kabataan!');
        }

        Log::notice('Email verification link rejected.', [
            'status' => $result['status'],
            'user_id' => $user instanceof User ? $user->getKey() : null,
        ]);

        $httpStatus = match ($result['status']) {
            'expired' => 410,
            'already_used' => 409,
            default => 400,
        };

        return response()->view('auth.email-verified', [
            'status' => $result['status'],
            'message' => $result['message'],
        ], $httpStatus);
    }

    public function checkVerificationStatus(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['verified' => false, 'status' => '']);
        }

        $user = Auth::user();
        $latestVerification = VerificationRequest::where('user_id', (string) $user->user_id)
            ->latest('created_at')
            ->first();
        $status = strtolower(optional($user->identityStatus)->status ?? '');

        if ($latestVerification?->isIncompleteDiditSession() === true) {
            $status = 'didit_started';
        }

        return response()->json([
            'verified' => $user->hasVerifiedEmail(),
            'status' => $status,
            'updated_at' => optional($user->identityStatus)->updated_at,
        ]);
    }

    //VERICATION RESEND
    public function resendVerification(Request $request)
    {
        $user = $request->user();

        if ($user->hasVerifiedEmail()) {
            $message = 'Your email is already verified.';

            if ($request->expectsJson()) {
                return $this->jsonAuthResponse('already_verified', $message, [
                    'redirect' => route('landpage'),
                ]);
            }

            return redirect()->route('landpage')->with('message', $message);
        }

        try {
            $user->sendEmailVerificationNotification();
        } catch (\Throwable $e) {
            Log::error('Email verification resend failed.', [
                'user_id' => $user->getKey(),
                'email_hash' => hash('sha256', (string) $user->email),
                'error' => $e::class,
            ]);

            $message = 'Verification email was not sent. Please try again in a moment or contact support if it continues.';

            if ($request->expectsJson()) {
                return $this->jsonAuthResponse('email_sending_failed', $message, [], 503);
            }

            return back()->with('mail_error', $message);
        }

        $message = 'Verification link sent! Please check your inbox or spam folder.';

        if ($request->expectsJson()) {
            return $this->jsonAuthResponse('verification_pending', $message, [
                'email_status' => 'sent',
            ]);
        }

        return back()->with('message', $message);
    }

    private function jsonAuthResponse(string $status, string $message, array $extra = [], int $httpStatus = 200)
    {
        return response()->json(array_merge([
            'status' => $status,
            'message' => $message,
        ], $extra), $httpStatus);
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
