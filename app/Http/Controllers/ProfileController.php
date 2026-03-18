<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\VerificationRequest;
use App\Models\User;
use App\Models\EventRegistration;
use App\Models\Event;
use App\Models\CampaignUpdate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use App\Models\IdentityStatus;
use Illuminate\Support\Facades\DB;
use App\Models\Campaign;
use App\Models\Donation;
use App\Models\ManualDonationRequest;
use Carbon\Carbon;
use Dompdf\Dompdf;
use Dompdf\Options;
use App\Notifications\CampaignUpdateNotification;
use App\Models\InKindDonation;
use App\Models\ImpactReport;

class ProfileController
{

    protected function noCacheView($view, $data = [])
    {
        $response = response()->view($view, $data);

        return $response->header('Cache-Control', 'no-cache, no-store, must-revalidate')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }


    public function profileview(Request $request)
    {
        if (!Auth::check()) {
            // Redirect to login if not logged in
            return redirect()->route('login.page')->with('error', 'Please log in to view your profile.');
        }

        $user = Auth::user();         // logged-in user
        $users = User::all();
        $latestRequest = VerificationRequest::where('user_id', $user->user_id)
            ->latest('created_at')
            ->first();


        return $this->noCacheView('profile.profile', compact('user', 'users', 'latestRequest'));
    }

    public function changePassword(Request $request)
    {
        $user = Auth::user();

        // Check if user has existing password (Google users won't)
        $hasPassword = !empty($user->password);

        if ($hasPassword) {
            // For users with existing password (regular users)
            $validator = Validator::make($request->all(), [
                'current_password' => 'required',
                'new_password' => 'required|min:8|confirmed',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'errors' => $validator->errors(),
                ], 422);
            }

            // Check current password
            if (!Hash::check($request->current_password, $user->password)) {
                return response()->json([
                    'status' => 'error',
                    'errors' => ['current_password' => ['Your current password is incorrect.']],
                ], 422);
            }
        } else {
            // For Google users setting up password for first time
            $validator = Validator::make($request->all(), [
                'new_password' => 'required|min:8|confirmed',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'errors' => $validator->errors(),
                ], 422);
            }
        }

        // Update password
        $user->update([
            'password' => Hash::make($request->new_password),
        ]);

        return response()->json([
            'status' => 'success',
            'message' => $hasPassword ? 'Password successfully updated.' : 'Password successfully set up.',
        ]);
    }
    //UPDATE PHOTO
    public function changePhoto(Request $request)
    {
        $request->validate([
            'photo' => 'required|image|max:2048',
        ]);

        $user = Auth::user();

        // Delete old image if stored locally
        if ($user->profile_photo_url && Storage::disk('public')->exists($user->profile_photo_url)) {
            Storage::disk('public')->delete($user->profile_photo_url);
        }

        // Store new photo
        $path = $request->file('photo')->store('profile_photos', 'public');

        // Update DB
        $user->update(['profile_photo_url' => $path]);

        return back()->with('success', 'Profile photo updated successfully!');
    }

    //UPDATE PROFILE
    public function update(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:50',
            'last_name'  => 'required|string|max:50',
            'phone_number' => 'nullable|string|max:20|regex:/^[0-9+\-\s()]+$/',
            'birthday' => 'nullable|date|before:today|after:1900-01-01',
        ]);

        $user = User::find(Auth::id());

        if ($user) {
            $user->first_name = $request->first_name;
            $user->last_name  = $request->last_name;

            if ($request->filled('phone_number')) {
                $user->phone_number = $request->phone_number;
            }

            // Only update birthday if provided (not empty)
            if ($request->filled('birthday')) {
                $user->birthday = $request->birthday;
            }
            $user->save();

            // ✅ Check if the request came via AJAX (JS fetch)
            if ($request->ajax()) {
                return response()->json(['success' => true, 'message' => 'Profile updated successfully!']);
            }

            // Normal redirect for non-AJAX requests
            return redirect()->back()->with('success', 'Profile updated successfully!');
        }

        if ($request->ajax()) {
            return response()->json(['success' => false, 'message' => 'User not found.']);
        }

        return redirect()->back()->with('error', 'User not found.');
    }

    //VERFICATION
    public function verifypage(Request $request)
    {
        $userId = Auth::id();

        $verificationRequest = VerificationRequest::where('user_id', $userId)
            ->latest()
            ->first();

        return view('profile.account.verification', compact('verificationRequest'));
    }

    public function submitverifcation(Request $r)
    {
        $user   = $r->user();
        $userId = $user->user_id; // ✅ using custom PK

        // 🔹 Re-upload flow
        if ($r->input('mode') === 'reupload') {
            $vr = VerificationRequest::where('user_id', $userId)
                ->orderByDesc('created_at')
                ->first();

            if (!$vr) {
                return back()->withErrors(['server' => 'No existing verification found to re-upload.'])->withInput();
            }

            // These fields were requested for reupload (saved in DB)
            $reuploadFields = $vr->reupload_fields ?? [];

            $rules = [];

            // Only require what admin requested
            if (in_array('id_front', $reuploadFields)) {
                $rules['id_front'] = 'required|image|mimes:jpeg,png,webp|max:7168';
            }
            if (in_array('id_back', $reuploadFields)) {
                $rules['id_back'] = 'required|image|mimes:jpeg,png,webp|max:7168';
            }
            if (in_array('face_photo', $reuploadFields)) {
                $rules['face_photo'] = 'required|image|mimes:jpeg,png,webp|max:7168';
            }
            if (in_array('selfie', $reuploadFields)) {
                $rules['selfie'] = 'required|image|mimes:jpeg,png,webp|max:7168';
            }

            $r->validate($rules);

            // Save only updated files
            if ($r->hasFile('id_front')) {
                $vr->id_front_path = $r->file('id_front')->store('kyc/ids', 'public');
            }
            if ($r->hasFile('id_back')) {
                $vr->id_back_path = $r->file('id_back')->store('kyc/ids', 'public');
            }
            if ($r->hasFile('face_photo')) {
                $vr->face_photo_path = $r->file('face_photo')->store('kyc/faces', 'public');
            }
            if ($r->hasFile('selfie')) {
                $vr->selfie_path = $r->file('selfie')->store('kyc/selfies', 'public');
            }

            $vr->status = 'pending';
            $vr->review_notes = null;
            $vr->save();

            IdentityStatus::updateOrCreate(
                ['user_id' => $userId],
                ['status'  => 'pending']
            );

            return redirect()->route('profile')
                ->with(['message' => 'Thanks! Your images were re-uploaded for review.']);
        }


        $r->validate([
            'id_type'   => 'required|in:philid,drivers_license',
            'id_number' => ['required', 'string', 'max:40', function ($attr, $value, $fail) use ($r) {
                if (
                    $r->id_type === 'philid' &&
                    !preg_match('/^(\d{4}-?\d{4}-?\d{4}|\d{4}-?\d{4}-?\d{4}-?\d{4})$/', $value)
                ) {
                    return $fail('PhilSys ID must be 12 or 16 digits (with or without dashes). Example: 1234-5678-9012 or 1234-5678-9012-3456');
                }

                if (
                    $r->id_type === 'drivers_license' &&
                    !preg_match('/^[A-Z]\d{2}-?\d{2}-?\d{6}$/i', $value)
                ) {
                    return $fail('Driver’s License must be in the format E12-23-000386 or E1223000386.');
                }
            }],
            'first_name'  => 'required|string|max:80',
            'middle_name' => 'nullable|string|max:80',
            'last_name'   => 'required|string|max:80',
            'dob'         => 'required|date',
            'sex'         => 'required|in:M,F',

            // Images
            'id_front'    => 'required|image|mimes:jpeg,png,webp|max:7168',
            'id_back'     => 'nullable|image|mimes:jpeg,png,webp|max:7168',
            'id_expiry'   => 'nullable|date',
            'face_photo'  => 'required|image|mimes:jpeg,png,webp|max:7168',
            'selfie'      => 'required|image|mimes:jpeg,png,webp|max:7168',
        ]);

        if ($r->id_type === 'drivers_license') {
            $r->validate([
                'id_back'   => 'required|image|mimes:jpeg,png,webp|max:7168',
                'id_expiry' => 'required|date|after:today',
            ]);
        }

        // Store files
        $frontPath  = $r->file('id_front')->store('kyc/ids', 'public');
        $backPath   = $r->hasFile('id_back') ? $r->file('id_back')->store('kyc/ids', 'public') : null;
        $facePath   = $r->file('face_photo')->store('kyc/faces', 'public');
        $selfiePath = $r->file('selfie')->store('kyc/selfies', 'public');

        $idHash = hash('sha256', $r->id_number);

        if (VerificationRequest::where('id_number', $r->id_number)->exists()) {
            return back()->withErrors([
                'id_number' => 'This ID number has already been used.'
            ])->withInput();
        }

        DB::beginTransaction();
        try {
            $vr = VerificationRequest::create([
                'user_id'        => $userId,
                'id_type'        => $r->id_type,
                'id_number'      => $r->id_number,
                'id_number_hash' => $idHash,
                'dob'            => $r->dob,
                'sex'            => $r->sex,
                'first_name'     => $r->first_name,
                'middle_name'    => $r->middle_name,
                'last_name'      => $r->last_name,
                'address'        => $r->address,
                'id_expiry'      => $r->id_expiry,
                'id_front_path'  => $frontPath,
                'id_back_path'   => $backPath,
                'face_photo_path' => $facePath,
                'selfie_path'    => $selfiePath,
                'supporting_doc_path' => null,
                'status'         => 'pending',
            ]);

            IdentityStatus::updateOrCreate(
                ['user_id' => $userId],
                ['status'  => 'pending']
            );

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withErrors([
                'server' => 'Failed to save verification. ' . $e->getMessage()
            ])->withInput();
        }

        return redirect()
            ->route('profile')
            ->with(['message' => 'Submitted! Your verification is now pending review.']);
    }


    // =============================CAMPAIGN DASHBOARD PROFILE=====================================================
    public function profiledash(Request $request)
    {

        if (!Auth::check()) {
            // Redirect to login if not logged in
            return redirect()->route('login.page')->with('error', 'Please log in to view your profile.');
        }

        $filter = $request->get('filter', 'all'); // default: all

        $query = Campaign::where('user_id', Auth::id());

        if ($filter === 'active') {
            $query->where('status', 'active');
        } elseif ($filter === 'scheduled') {
            $query->where('status', 'scheduled');
        } elseif ($filter === 'ended') {
            $query->where(function ($q) {
                $q->where('status', 'ended')
                    ->orWhere('ends_at', '<', now()); // auto-detect expired campaigns
            });
        }

        $campaigns = $query->orderBy('created_at', 'desc')->get();

        // Fetch donations grouped by campaign_id
        $donations = Donation::whereIn('campaign_id', $campaigns->pluck('campaign_id'))
            ->get()
            ->groupBy('campaign_id');

        return $this->noCacheView('profile.profiledashboard', compact('campaigns', 'donations', 'filter'));
    }

    public function profileDashData()
    {
        $campaigns = Campaign::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();
        $donations = Donation::whereIn('campaign_id', $campaigns->pluck('campaign_id'))
            ->get()
            ->groupBy('campaign_id');

        return view('profile.partials.campaigncard', compact('campaigns', 'donations'));
    }

    //DELETE CAMPAIGN

    public function destroy(Request $request, $id)
    {
        $campaign = Campaign::findOrFail($id);
        $campaign->delete();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Campaign deleted successfully.'
            ]);
        }

        return redirect()->back()->with('success', 'Campaign deleted successfully.');
    }


    // ENDED CAMPAIGN
    public function end($id)
    {
        $campaign = Campaign::find($id);

        // Return success silently if campaign does not exist or already ended
        if (!$campaign || $campaign->status === 'ended') {
            return response()->json(['success' => true]);
        }

        // End the campaign
        $campaign->update(['status' => 'ended']);

        return response()->json(['success' => true]);
    }

    //CAMPAIGN ANALYTICS CARD
    public function analytics()
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'error' => 'Not authenticated'
            ], 401);
        }

        $userId = $user->user_id; // ✅ works because your User model PK is user_id

        $campaignsQuery = Campaign::where('user_id', $userId);

        return response()->json([
            'success' => true,
            'data' => [
                'campaigns_created' => $campaignsQuery->count(),
                'active_campaigns'  => (clone $campaignsQuery)->where('status', 'active')->count(),
                'ended_campaigns'   => (clone $campaignsQuery)->where('status', 'ended')->count(),
                'total_donations'   => Donation::whereHas('campaign', function ($q) use ($userId) {
                    $q->where('user_id', $userId);
                })->sum('amount'),
            ]
        ]);
    }

    //CAMPAIGN ANALYTICS CHART
    public function donationsOverTime(Request $request)
    {
        $user = Auth::user();
        $startDate = Carbon::now()->subDays(30)->startOfDay();
        $endDate   = Carbon::now()->endOfDay();

        $campaignId = $request->query('campaign_id');

        $donations = Donation::whereHas('campaign', function ($q) use ($user, $campaignId) {
            $q->where('user_id', $user->user_id);
            if ($campaignId && $campaignId !== "all") {
                $q->where('campaign_id', $campaignId);
            }
        })
            ->whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('DATE(created_at) as date, SUM(amount) as total')
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->pluck('total', 'date')
            ->mapWithKeys(function ($total, $date) {
                return [Carbon::parse($date)->format('Y-m-d') => $total];
            });

        $labels = [];
        $data   = [];
        $period = new \DatePeriod($startDate, new \DateInterval('P1D'), $endDate->copy()->addDay());

        foreach ($period as $date) {
            $day = $date->format('Y-m-d');
            $labels[] = $date->format('M d');
            $data[]   = $donations[$day] ?? 0;
        }

        return response()->json([
            'success' => true,
            'labels'  => $labels,
            'data'    => $data
        ]);
    }

    // TOP CHARTS
    public function topCampaigns(Request $request)
    {
        $user = Auth::user();
        $filter = $request->get('filter', 'all');

        $query = Campaign::where('user_id', $user->user_id)
            ->withSum('donations as total_donations', 'amount');

        if ($filter === 'active') {
            $query->where('status', 'active');
        } elseif ($filter === 'ended') {
            $query->where('status', 'ended');
        }

        $campaigns = $query->orderByDesc('total_donations')
            ->take(5)
            ->get(['campaign_id', 'title']);

        return response()->json([
            'success' => true,
            'labels'  => $campaigns->pluck('title'),
            'data'    => $campaigns->pluck('total_donations')
        ]);
    }

    /// STATISTIC TO DONATIONS MADE
    public function myDonationsAnalytics()
    {
        $user = Auth::user();

        // Total donations by this logged-in user
        $total = Donation::where('user_id', $user->user_id)
            ->sum('amount');

        // Donations this month
        $thisMonth = Donation::where('user_id', $user->user_id)
            ->whereMonth('created_at', now()->month)
            ->sum('amount');

        // Recent 5 donations with campaign info
        $recent = Donation::with('campaign')
            ->where('user_id', $user->user_id)
            ->latest()
            ->take(5)
            ->get();

        return response()->json([
            'success' => true,
            'total'   => $total,
            'thisMonth' => $thisMonth,
            'recent'  => $recent->map(fn($d) => [
                'campaign' => $d->campaign?->title ?? 'Unknown Campaign',
                'amount'   => $d->amount,
                'date'     => $d->created_at->diffForHumans(),
            ])
        ]);
    }

    // VIEW ALL DONATION MODAL
    public function allMyDonations()
    {
        $user = Auth::user();

        $donations = Donation::with('campaign')
            ->where('user_id', $user->user_id)
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'donations' => $donations->map(fn($d) => [
                'campaign' => $d->campaign?->title ?? 'Unknown Campaign',
                'amount'   => $d->amount,
                'date'     => $d->created_at->toDayDateTimeString(),
            ])
        ]);
    }

    //MINI FEED RECENT DONATION
    public function recentDonations()
    {
        $user = Auth::user();

        $donations = Donation::with('campaign')
            ->whereHas('campaign', fn($q) => $q->where('user_id', $user->user_id))
            ->latest()
            ->take(5)
            ->get();

        return response()->json([
            'success' => true,
            'donations' => $donations->map(fn($d) => [
                'donor'    => $d->donor_name ?? 'Anonymous',
                'amount'   => $d->amount,
                'campaign' => $d->campaign?->title ?? 'Unknown Campaign',
                'date'     => $d->created_at->diffForHumans(),
            ])
        ]);
    }

    //DONATION MANUAL REQUEST
    public function reportFake(Donation $donation)
    {
        if ($donation->campaign->user_id !== Auth::id()) {
            return request()->ajax()
                ? response()->json(['success' => false, 'error' => 'Unauthorized action.'])
                : back()->with('error', 'Unauthorized action.');
        }

        if ($donation->status === 'fake') {
            return request()->ajax()
                ? response()->json(['success' => false, 'error' => 'Already marked as fake.'])
                : back()->with('info', 'This donation is already marked as fake.');
        }

        $campaign = $donation->campaign;
        $campaign->decrement('current_amount', $donation->amount);

        $donation->delete();

        return request()->ajax()
            ? response()->json(['success' => true, 'message' => 'Donation flagged as fake and removed.'])
            : back()->with('success', 'Donation flagged as fake and removed from records.');
    }

    public function manualadd(Request $request, $campaignId)
    {
        $campaign = Campaign::findOrFail($campaignId);

        if ($campaign->user_id !== Auth::id()) {
            return $request->ajax()
                ? response()->json(['success' => false, 'error' => 'Unauthorized action.'], 403)
                : back()->with('error', 'Unauthorized action.');
        }

        $validated = $request->validate([
            'amount' => 'required|numeric|min:1',
            'reference_number' => 'nullable|string|max:100',
            'proof_image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $proofPath = null;
        if ($request->hasFile('proof_image')) {
            $proofPath = $request->file('proof_image')->store('manual_proofs', 'public');
        }

        ManualDonationRequest::create([
            'campaign_id'      => $campaign->campaign_id,
            'user_id'          => Auth::id(),
            'amount'           => $validated['amount'],
            'reference_number' => $validated['reference_number'] ?? null,
            'proof_image'      => $proofPath,
            'status'           => 'pending',
        ]);

        // 🔑 If AJAX, return JSON, else fallback to redirect
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Manual donation request submitted! Waiting for admin approval.'
            ]);
        }

        return back()->with('success', 'Manual donation request submitted! Waiting for admin approval.');
    }

    //ADDED FUNCTION PDF
    public function exportDonationsPDF(Campaign $campaign)
    {
        if ($campaign->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $donations = Donation::where('campaign_id', $campaign->campaign_id)
            ->with(['user', 'campaign'])
            ->orderBy('created_at', 'desc')
            ->get();

        $data = [
            'donations' => $donations,
            'campaign' => $campaign,
            'totalRaised' => $donations->sum('amount')
        ];

        // Configure DomPDF options
        $options = new Options();
        $options->set('isRemoteEnabled', true);
        $options->set('isHtml5ParserEnabled', true);
        $options->set('defaultFont', 'Arial');

        $dompdf = new Dompdf($options);

        // Load HTML content
        $html = view('profile.exports.donations-pdf', $data)->render();
        $dompdf->loadHtml($html);

        // Set paper size and orientation
        $dompdf->setPaper('A4', 'landscape');

        // Render PDF
        $dompdf->render();

        $filename = "Donations-Report-" . str_replace(' ', '-', $campaign->title) . "-" . now()->format('M-d-Y') . ".pdf";

        // Return PDF as download
        return $dompdf->stream($filename);
    }

    //UPDATE CAMPAIGN and Reaction
    public function storeUpdate(Request $request)
    {
        $request->validate([
            'campaign_id' => 'required|exists:campaigns,campaign_id',
            'message' => 'required|string|max:1000',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
        ]);

        $campaign = Campaign::findOrFail($request->campaign_id);

        // Check if user owns the campaign
        if ($campaign->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'error' => 'Unauthorized to update this campaign.'
            ], 403);
        }

        $imagePaths = [];

        // Handle multiple image uploads
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('campaign-updates', 'public');
                $imagePaths[] = $path;
            }
        }

        // Create the update
        $update = CampaignUpdate::create([
            'campaign_id' => $request->campaign_id,
            'user_id' => Auth::id(),
            'message' => $request->message,
            'images' => !empty($imagePaths) ? $imagePaths : null,
        ]);

        // Load relationships for response
        $update->load('organizer');

        $this->notifyCampaignDonors($campaign, $update);

        return response()->json([
            'success' => true,
            'message' => 'Update posted successfully!',
            'update' => $update
        ]);
    }

    /**
     * Send notifications to all donors of the campaign
     */
    private function notifyCampaignDonors(Campaign $campaign, CampaignUpdate $update)
    {
        $donors = Donation::where('campaign_id', $campaign->campaign_id)
            ->with('user')
            ->get()
            ->pluck('user')
            ->filter()
            ->unique('user_id');

        // Send notification to each donor
        foreach ($donors as $donor) {
            if ($donor->user_id !== Auth::id()) {
                $donor->notify(new CampaignUpdateNotification($campaign, $update));
            }
        }
    }

    // =====================================================================================================

    // =============================EVENT DASHBOARD PROFILE==================================================

    public function profileevent()
    {

        if (!Auth::check()) {
            // Redirect to login if not logged in
            return redirect()->route('login.page')->with('error', 'Please log in to view your profile.');
        }

        $userId = Auth::id(); // current logged-in user
        $registrations = EventRegistration::with('event', 'volunteerRoles')
            ->where('user_id', $userId)
            ->get();

        return $this->noCacheView('profile.profileevent', compact('registrations'));
    }


    public function refreshProfileEvents()
    {
        $userId = Auth::id();

        if (!$userId) {
            return response()->json([
                'success' => false,
                'message' => 'User not authenticated'
            ], 401);
        }

        $registrations = EventRegistration::with(['event', 'volunteerRoles'])
            ->where('user_id', $userId)
            ->orderBy('registration_id', 'desc')
            ->get();

        $now = now();
        $upcoming = $registrations->filter(function ($reg) use ($now) {
            return $reg->status === 'registered' &&
                $reg->event &&
                $now->lt($reg->event->start_date);
        })->count();

        $ongoing = $registrations->filter(function ($reg) use ($now) {
            return $reg->status === 'registered' &&
                $reg->event &&
                $now->between($reg->event->start_date, $reg->event->end_date);
        })->count();

        return response()->json([
            'success' => true,
            'html' => view('profile.partials.event-list', compact('registrations'))->render(),
            'stats' => [
                'joined' => $registrations->count(),
                'attended' => $registrations->where('status', 'attended')->count(),
                'absent' => $registrations->where('status', 'absent')->count(),
                'upcoming' => $upcoming,
                'ongoing' => $ongoing,
            ]
        ]);
    }

    public function eventUnregister(Event $event)
    {
        $user = Auth::user();

        // Find registration
        $registration = EventRegistration::where('user_id', $user->user_id)
            ->where('event_id', $event->event_id)
            ->first();

        if (! $registration) {
            return response()->json([
                'success' => false,
                'message' => 'You are not registered for this event.'
            ]);
        }

        // Optional: basic time restriction (keep only this lightweight check)
        $now = Carbon::now();
        $start = Carbon::parse($event->start_date);

        if ($now->greaterThanOrEqualTo($start)) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot unregister from an event that has already started.'
            ]);
        }

        // Proceed with deletion
        $registration->delete();

        return response()->json([
            'success' => true,
            'message' => 'You have successfully unregistered from this event.'
        ]);
    }


    public function refreshEvents()
    {
        $user = Auth::user();
        $registrations = EventRegistration::with(['event', 'volunteerRoles'])
            ->where('user_id', $user->user_id)
            ->get();

        return response()->json([
            'success' => true,
            'registrations' => $registrations
        ]);
    }


    public function refreshStats()
    {
        $userId = Auth::id();

        $registered = EventRegistration::where('user_id', $userId)->count();
        $attended = EventRegistration::where('user_id', $userId)
            ->where('status', 'attended')
            ->count();
        $absent = EventRegistration::where('user_id', $userId)
            ->where('status', 'absent')
            ->count();

        return response()->json([
            'success' => true,
            'joined' => $registered,
            'attended' => $attended,
            'missed' => $absent,
            'trends' => [
                'joined' => '+3 this month',
                'attended' => '+8 hours',
                'missed' => 'Next: ' . now()->format('M j')
            ]
        ]);
    }


    // =====================================================================================================

    // =============================In-Kind DASHBOARD PROFILE==================================================

    public function profileinkind()
    {

        if (!Auth::check()) {
            // Redirect to login if not logged in
            return redirect()->route('login.page')->with('error', 'Please log in to view your profile.');
        }


        $inkindDonations = InKindDonation::with('dropoffPoint')
            ->where('user_id', Auth::id())
            ->latest()
            ->get();

        return $this->noCacheView('profile.profileinkind', compact('inkindDonations'));
    }

    public function cancelInKind(InKindDonation $donation)
    {
        if ($donation->user_id !== Auth::id()) {
            return response()->json(['success' => false, 'error' => 'Unauthorized'], 403);
        }

        $donation->status = 'Cancelled';
        $donation->save();

        return response()->json([
            'success' => true,
            'message' => 'Donation has been cancelled.',
            'donation_id' => $donation->inkind_id
        ]);
    }

    public function deleteInKind(InKindDonation $donation)
    {
        if ($donation->user_id !==  Auth::id()) {
            return response()->json(['success' => false, 'error' => 'Unauthorized'], 403);
        }

        $donation->delete();

        return response()->json([
            'success' => true,
            'message' => 'Donation has been deleted.',
            'donation_id' => $donation->inkind_id
        ]);
    }

    public function list()
    {
        $donations = InKindDonation::with('dropoffPoint')
            ->where('user_id', Auth::id()) // only current user's donations
            ->latest()
            ->get()
            ->map(function ($donation) {
                return [
                    'inkind_id'    => $donation->inkind_id,
                    'item_name'    => $donation->item_name,
                    'category'     => $donation->category,
                    'quantity'     => $donation->quantity,
                    'unit'         => $donation->unit ?? 'items',
                    'status'       => $donation->status,
                    'description'  => $donation->description,
                    'dropoffPoint' => [
                        'name' => $donation->dropoffPoint->name ?? 'N/A',
                    ],
                    // always show created_at since no delivery date
                    'created_at'   => $donation->created_at->format('M d, Y h:i A'),
                ];
            });

        return response()->json([
            'success'   => true,
            'donations' => $donations,
        ]);
    }
    // =====================================================================================================
    public function aboutus()
    {

        // Get ended events
        $endedEvents = Event::where('end_date', '<', now())
            ->orderBy('end_date', 'desc') // Limit to 3 recent events
            ->get();

        // Get impact reports
        $impactReports = ImpactReport::with('donations')
            ->orderBy('report_date', 'desc') // Limit to 3 recent reports
            ->get();

        // Get ended campaigns (NEW)
        $endedCampaigns = Campaign::where('status', 'completed')
            ->orWhere('ends_at', '<', now())
            ->orderBy('ends_at', 'desc')
            ->get();

        return $this->noCacheView('aboutus', compact(
            'endedEvents',
            'impactReports',
            'endedCampaigns'
        ));
    }
}
