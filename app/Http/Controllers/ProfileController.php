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
use App\Models\Campaign;
use App\Models\Donation;
use App\Models\ManualDonationRequest;
use Carbon\Carbon;
use Dompdf\Dompdf;
use Dompdf\Options;
use App\Notifications\CampaignUpdateNotification;
use App\Models\InKindDonation;
use App\Models\ImpactReport;
use App\Services\Storage\R2StorageService;
use App\Services\Storage\R2StorageException;
use App\Services\Verification\IdVerificationService;
use App\Services\Verification\VerificationException;
use App\Services\Uploads\ChunkUploadService;

class ProfileController
{
    /**
     * Centralized R2 storage service. All file upload/replace/delete work for
     * this controller must go through this instance — never call Storage::
     * directly for user-uploaded files.
     */
    protected R2StorageService $storage;

    /**
     * Automated ID verification orchestrator. Runs after a verification
     * record is created so we can short-circuit obvious approvals /
     * rejections before they hit the admin queue.
     */
    protected IdVerificationService $idVerification;
    protected ChunkUploadService $chunkUploads;

    public function __construct(R2StorageService $storage, IdVerificationService $idVerification, ChunkUploadService $chunkUploads)
    {
        $this->storage = $storage;
        $this->idVerification = $idVerification;
        $this->chunkUploads = $chunkUploads;
    }

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
        $latestRequest = VerificationRequest::where('user_id', $user->user_id)
            ->latest('created_at')
            ->first();


        return $this->noCacheView('profile.profile', compact('user', 'latestRequest'));
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
            'photo' => 'required_without:photo_uploaded_path|image|max:8192',
            'photo_uploaded_path' => 'nullable|string|max:500',
        ]);

        $user = Auth::user();

        try {
            $newKey = $this->completedChunkPath($request, 'photo_uploaded_path', 'profile_photo');
            if ($newKey) {
                $this->storage->delete($user->profile_photo_url);
            } else {
                // Upload new photo to R2 and remove the previous one (if it was on R2).
                $newKey = $this->storage->replace(
                    $request->file('photo'),
                    $user->profile_photo_url,
                    'profile_photos',
                    ['max_kb' => 8192, 'mimes' => config('r2.validation.image_mimes')]
                );
            }
        } catch (R2StorageException $e) {
            return back()->with('error', 'File upload failed. Please try again.');
        }

        $user->update(['profile_photo_url' => $newKey]);

        return back()->with('success', 'Profile photo updated successfully!');
    }

    //UPDATE PROFILE
    public function update(Request $request)
    {
        if ($request->has('phone_number')) {
            $phoneNumber = preg_replace('/\D+/', '', (string) $request->input('phone_number'));
            $request->merge([
                'phone_number' => $phoneNumber !== '' ? $phoneNumber : null,
            ]);
        }

        $request->validate([
            'first_name' => 'required|string|max:50',
            'last_name'  => 'required|string|max:50',
            'phone_number' => 'nullable|regex:/^09\d{9}$/',
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
        $userId = (string) Auth::user()->user_id;

        $verificationRequest = VerificationRequest::where('user_id', $userId)
            ->latest()
            ->first();

        return view('profile.account.verification', compact('verificationRequest'));
    }

    public function submitverifcation(Request $r)
    {
        $user   = $r->user();
        // Tighter limits for ID images come from config/id_verification.php
        // so the same value is enforced from .env across environments.
        $idMaxKb = ((int) config('id_verification.file.max_size_mb', 5)) * 1024;
        $allowedTypes = implode(',', (array) config('id_verification.file.allowed_types', ['jpg', 'jpeg', 'png', 'webp']));
        $verificationImageMaxKb = (int) config('r2.validation.max_size_kb', 16384);
        $requiredIdImage = "required|image|mimes:{$allowedTypes}|max:{$idMaxKb}";
        $nullableIdImage = "nullable|image|mimes:{$allowedTypes}|max:{$idMaxKb}";
        $requiredVerificationImage = "required|image|mimes:jpeg,png,webp|max:{$verificationImageMaxKb}";
        $nullableVerificationImage = "nullable|image|mimes:jpeg,png,webp|max:{$verificationImageMaxKb}";
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
                $rules['id_front'] = "required_without:id_front_uploaded_path|image|mimes:{$allowedTypes}|max:{$idMaxKb}";
                $rules['id_front_uploaded_path'] = 'nullable|string|max:500';
            }
            if (in_array('id_back', $reuploadFields)) {
                $rules['id_back'] = "required_without:id_back_uploaded_path|image|mimes:{$allowedTypes}|max:{$idMaxKb}";
                $rules['id_back_uploaded_path'] = 'nullable|string|max:500';
            }
            if (in_array('face_photo', $reuploadFields)) {
                $rules['face_photo'] = 'required_without:face_photo_uploaded_path|image|mimes:jpeg,png,webp|max:' . $verificationImageMaxKb;
                $rules['face_photo_uploaded_path'] = 'nullable|string|max:500';
            }
            if (in_array('selfie', $reuploadFields)) {
                $rules['selfie'] = 'required_without:selfie_uploaded_path|image|mimes:jpeg,png,webp|max:' . $verificationImageMaxKb;
                $rules['selfie_uploaded_path'] = 'nullable|string|max:500';
            }

            $r->validate($rules);

            // Pre-flight signature & per-user attempt checks for the ID images.
            try {
                if ($r->hasFile('id_front')) {
                    $this->idVerification->validateUpload($r->file('id_front'), $userId);
                }
                if ($r->hasFile('id_back')) {
                    $this->idVerification->validateUpload($r->file('id_back'), $userId);
                }
            } catch (VerificationException $e) {
                return back()->withErrors(['id_front' => $e->userMessage()])->withInput();
            }

            try {
                // Save only updated files — each replace uploads to R2 and
                // deletes the previous object on success.
                if ($r->hasFile('id_front')) {
                    $vr->id_front_path = $this->storage->replace(
                        $r->file('id_front'), $vr->id_front_path, 'kyc_ids',
                        ['mimes' => ['image/jpeg', 'image/png', 'image/webp']]
                    );
                } elseif ($path = $this->completedChunkPath($r, 'id_front_uploaded_path', 'kyc_id')) {
                    $this->storage->delete($vr->id_front_path);
                    $vr->id_front_path = $path;
                }
                if ($r->hasFile('id_back')) {
                    $vr->id_back_path = $this->storage->replace(
                        $r->file('id_back'), $vr->id_back_path, 'kyc_ids',
                        ['mimes' => ['image/jpeg', 'image/png', 'image/webp']]
                    );
                } elseif ($path = $this->completedChunkPath($r, 'id_back_uploaded_path', 'kyc_id')) {
                    $this->storage->delete($vr->id_back_path);
                    $vr->id_back_path = $path;
                }
                if ($r->hasFile('face_photo')) {
                    $vr->face_photo_path = $this->storage->replace(
                        $r->file('face_photo'), $vr->face_photo_path, 'kyc_faces',
                        ['mimes' => ['image/jpeg', 'image/png', 'image/webp']]
                    );
                } elseif ($path = $this->completedChunkPath($r, 'face_photo_uploaded_path', 'kyc_face')) {
                    $this->storage->delete($vr->face_photo_path);
                    $vr->face_photo_path = $path;
                }
                if ($r->hasFile('selfie')) {
                    $vr->selfie_path = $this->storage->replace(
                        $r->file('selfie'), $vr->selfie_path, 'kyc_selfies',
                        ['mimes' => ['image/jpeg', 'image/png', 'image/webp']]
                    );
                } elseif ($path = $this->completedChunkPath($r, 'selfie_uploaded_path', 'kyc_selfie')) {
                    $this->storage->delete($vr->selfie_path);
                    $vr->selfie_path = $path;
                }
            } catch (R2StorageException $e) {
                return back()->withErrors(['server' => 'File upload failed. Please try again.'])->withInput();
            }

            $vr->status = 'pending';
            $vr->review_notes = null;
            // Reset automated decision metadata so the new submission is judged fresh.
            $vr->decision_source = null;
            $vr->decision_reason = null;
            $vr->confidence_score = null;
            $vr->fraud_warnings = null;
            $vr->reupload_fields = null;
            $vr->save();

            IdentityStatus::updateOrCreate(
                ['user_id' => $userId],
                ['status'  => 'pending']
            );

            // Re-run the automated review against the new image(s).
            try {
                $this->idVerification->runAutomatedReview($vr->fresh(), $user);
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning('[IdVerification] Reupload review failed', [
                    'error' => $e::class,
                ]);
            }

            return redirect()->route('profile')
                ->with(['message' => 'Thanks! Your images were re-uploaded. We are reviewing them now.']);
        }


        if ($r->input('id_type') === 'philid') {
            $digits = preg_replace('/\D+/', '', (string) $r->input('id_number'));
            $r->merge([
                'id_number' => implode('-', str_split($digits, 4)),
            ]);
        } elseif ($r->input('id_type') === 'drivers_license') {
            $compactLicense = strtoupper(preg_replace('/[^A-Z0-9]/i', '', (string) $r->input('id_number')));

            if (preg_match('/^([A-Z])(\d{10})$/', $compactLicense, $matches)) {
                $digits = $matches[2];
                $compactLicense = $matches[1] . substr($digits, 0, 2) . '-' . substr($digits, 2, 2) . '-' . substr($digits, 4, 6);
            }

            $r->merge([
                'id_number' => $compactLicense,
            ]);
        }

        $r->validate([
            'id_type'   => 'required|in:philid,drivers_license',
            'id_number' => ['required', 'string', 'max:40', function ($attr, $value, $fail) use ($r) {
                if (
                    $r->id_type === 'philid' &&
                    !preg_match('/^(\d{4}-\d{4}-\d{4}|\d{4}-\d{4}-\d{4}-\d{4})$/', $value)
                ) {
                    return $fail('PhilSys ID must be exactly 12 or 16 digits. Example: 1234-5678-9012 or 1234-5678-9012-3456');
                }

                if (
                    $r->id_type === 'drivers_license' &&
                    !preg_match('/^[A-Z]\d{2}-\d{2}-\d{6}$/', $value)
                ) {
                    return $fail('Driver’s License must be in the format E12-23-000386 or E1223000386.');
                }
            }],
            'first_name'  => 'required|string|max:80',
            'middle_name' => 'nullable|string|max:80',
            'last_name'   => 'required|string|max:80',
            'dob'         => 'required|date',
            'sex'         => 'required|in:M,F',

            // Images: front, back, and selfie are all required so the
            // automated review has the full set of evidence to score.
            // The face-only photo is optional (legacy) — the selfie
            // already establishes face presence and ID-holder context.
            'id_front'    => "required_without:id_front_uploaded_path|image|mimes:{$allowedTypes}|max:{$idMaxKb}",
            'id_front_uploaded_path' => 'nullable|string|max:500',
            'id_back'     => "required_without:id_back_uploaded_path|image|mimes:{$allowedTypes}|max:{$idMaxKb}",
            'id_back_uploaded_path' => 'nullable|string|max:500',
            'id_expiry'   => 'nullable|date',
            'face_photo'  => $nullableVerificationImage,
            'face_photo_uploaded_path' => 'nullable|string|max:500',
            'selfie'      => 'required_without:selfie_uploaded_path|image|mimes:jpeg,png,webp|max:' . $verificationImageMaxKb,
            'selfie_uploaded_path' => 'nullable|string|max:500',
        ]);

        if ($r->id_type === 'drivers_license') {
            $r->validate([
                'id_expiry' => 'required|date|after:today',
            ]);
        }

        // Pre-flight signature, MIME and per-user attempt checks for the ID images.
        try {
            $this->idVerification->validateUpload($r->file('id_front'), $userId);
            $this->idVerification->validateUpload($r->file('id_back'), $userId);
        } catch (VerificationException $e) {
            return back()->withErrors(['id_front' => $e->userMessage()])->withInput();
        }

        // Store files on Cloudflare R2. If any upload fails, surface the error
        // to the user without creating a half-written verification record.
        // Both ID front and back are required so the orchestrator can OCR
        // both sides for a more accurate match. Face photo is optional —
        // the selfie-with-ID covers the face-presence requirement.
        try {
            $frontPath  = $this->completedChunkPath($r, 'id_front_uploaded_path', 'kyc_id')
                ?: $this->storage->upload($r->file('id_front'), 'kyc_ids',
                    ['mimes' => ['image/jpeg', 'image/png', 'image/webp']]);
            $backPath   = $this->completedChunkPath($r, 'id_back_uploaded_path', 'kyc_id')
                ?: $this->storage->upload($r->file('id_back'), 'kyc_ids',
                    ['mimes' => ['image/jpeg', 'image/png', 'image/webp']]);
            $facePath   = $this->completedChunkPath($r, 'face_photo_uploaded_path', 'kyc_face')
                ?: ($r->hasFile('face_photo')
                ? $this->storage->upload($r->file('face_photo'), 'kyc_faces',
                    ['mimes' => ['image/jpeg', 'image/png', 'image/webp']])
                : null);
            $selfiePath = $this->completedChunkPath($r, 'selfie_uploaded_path', 'kyc_selfie')
                ?: $this->storage->upload($r->file('selfie'), 'kyc_selfies',
                    ['mimes' => ['image/jpeg', 'image/png', 'image/webp']]);
        } catch (R2StorageException $e) {
            return back()->withErrors(['server' => 'File upload failed. Please try again.'])->withInput();
        }

        $idHash = hash('sha256', $r->id_number);

        // Block duplicates ONLY across different users. The same user
        // resubmitting their own ID (e.g. after an automated rejection or
        // a failed OCR call) should be allowed — the previous record is
        // marked superseded below.
        $duplicate = VerificationRequest::where(function ($q) use ($idHash, $r) {
                $q->where('id_number_hash', $idHash)
                  ->orWhere('id_number', $r->id_number);
            })
            ->where('user_id', '!=', $userId)
            ->exists();

        if ($duplicate) {
            return back()->withErrors([
                'id_number' => 'This ID number is already linked to another account.'
            ])->withInput();
        }

        // Supersede the user's prior verification records for this ID so the
        // history is preserved but the new submission is the active one.
        VerificationRequest::where('user_id', $userId)
            ->whereIn('status', ['pending', 'rejected', 'reupload'])
            ->update(['status' => 'superseded']);

        $uploadedVerificationFiles = array_filter([$frontPath, $backPath, $facePath, $selfiePath]);
        $vr = null;

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
        } catch (\Throwable $e) {
            if ($vr && $vr->exists) {
                $vr->delete();
            }

            foreach ($uploadedVerificationFiles as $uploadedFile) {
                $this->storage->delete($uploadedFile);
            }

            return back()->withErrors([
                'server' => 'Failed to save verification. Please try again.'
            ])->withInput();
        }

        // Kick off automated review. Errors here must not block the user —
        // the orchestrator handles its own failures and falls back to manual
        // review, so we only catch unexpected exceptions here.
        $reviewResult = ['decision' => null, 'score' => null];
        try {
            $reviewResult = $this->idVerification->runAutomatedReview($vr->fresh(), $user);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('[IdVerification] Pipeline trigger failed', [
                'request_id' => (string) $vr->getKey(),
                'error' => $e::class,
            ]);
        }

        $userMessage = match ($reviewResult['decision']) {
            IdVerificationService::DECISION_APPROVED
                => 'Verified! Your account is now fully verified.',
            IdVerificationService::DECISION_REJECTED
                => 'Your verification could not be approved. Please review the message on your verification page.',
            IdVerificationService::DECISION_NEEDS_RESUBMISSION
                => 'Your ID image needs to be re-uploaded. Please check your verification page for details.',
            default
                => 'Submitted! Your verification has been received and is now under review.',
        };

        return redirect()
            ->route('profile')
            ->with(['message' => $userMessage]);
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
        $campaignIds = Campaign::where('user_id', $userId)
            ->get(['_id'])
            ->map(fn ($campaign) => $campaign->campaign_id)
            ->all();
        $totalDonations = Donation::whereIn('campaign_id', $campaignIds)
            ->get(['amount'])
            ->sum(fn ($donation) => (float) $donation->amount);

        return response()->json([
            'success' => true,
            'data' => [
                'campaigns_created' => $campaignsQuery->count(),
                'active_campaigns'  => (clone $campaignsQuery)->where('status', 'active')->count(),
                'ended_campaigns'   => (clone $campaignsQuery)->where('status', 'ended')->count(),
                'total_donations'   => $totalDonations,
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

        // Get user's campaign IDs first, then filter donations by those IDs
        $campaignQuery = Campaign::where('user_id', $user->user_id);
        if ($campaignId && $campaignId !== "all") {
            $campaignQuery->where('_id', $campaignId);
        }
        $campaignIds = $campaignQuery->get(['_id'])
            ->map(fn ($campaign) => $campaign->campaign_id)
            ->all();

        // Fetch donations and group by date in PHP (MongoDB-compatible)
        $rawDonations = Donation::whereIn('campaign_id', $campaignIds)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get(['amount', 'created_at']);

        $donations = $rawDonations->groupBy(function ($d) {
            return $d->created_at->format('Y-m-d');
        })->map(function ($group) {
            return $group->sum(fn ($donation) => (float) $donation->amount);
        });

        $labels = [];
        $data   = [];
        $period = new \DatePeriod($startDate, new \DateInterval('P1D'), $endDate->copy()->addDay());

        foreach ($period as $date) {
            $day = $date->format('Y-m-d');
            $labels[] = $date->format('M d');
            $data[]   = (float) ($donations[$day] ?? 0);
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

        $query = Campaign::where('user_id', $user->user_id);

        if ($filter === 'active') {
            $query->where('status', 'active');
        } elseif ($filter === 'ended') {
            $query->where('status', 'ended');
        }

        // MongoDB doesn't support withSum natively — compute in PHP
        $campaigns = $query->get(['_id', 'title']);
        $campaigns->each(function ($campaign) {
            $campaign->total_donations = Donation::where('campaign_id', $campaign->campaign_id)
                ->get(['amount'])
                ->sum(fn ($donation) => (float) $donation->amount);
        });

        $campaigns = $campaigns->sortByDesc('total_donations')->take(5)->values();

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
            ->get(['amount'])
            ->sum(fn ($donation) => (float) $donation->amount);

        // Donations this month
        $thisMonth = Donation::where('user_id', $user->user_id)
            ->whereMonth('created_at', now()->month)
            ->get(['amount'])
            ->sum(fn ($donation) => (float) $donation->amount);

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

        $campaignIds = Campaign::where('user_id', $user->user_id)
            ->get(['_id'])
            ->map(fn ($campaign) => $campaign->campaign_id)
            ->all();

        $donations = Donation::with('campaign')
            ->whereIn('campaign_id', $campaignIds)
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
        $campaign->adjustDonationStats(-((float) $donation->amount), 0);

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

        if ($request->has('reference_number')) {
            $referenceNumber = preg_replace('/\D+/', '', (string) $request->input('reference_number'));
            $request->merge([
                'reference_number' => $referenceNumber !== '' ? $referenceNumber : null,
            ]);
        }

        $validated = $request->validate([
            'amount' => 'required|numeric|min:1',
            'reference_number' => 'nullable|regex:/^\d{5,30}$/',
            'proof_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:8192',
            'proof_image_uploaded_path' => 'nullable|string|max:500',
        ]);

        $proofPath = $this->completedChunkPath($request, 'proof_image_uploaded_path', 'manual_donation_proof');
        if ($request->hasFile('proof_image')) {
            try {
                $proofPath = $this->storage->upload($request->file('proof_image'), 'manual_donation_proofs',
                    ['max_kb' => 8192, 'mimes' => ['image/jpeg', 'image/png', 'image/webp']]);
            } catch (R2StorageException $e) {
                return $request->ajax()
                    ? response()->json(['success' => false, 'error' => 'File upload failed. Please try again.'], 422)
                    : back()->with('error', 'File upload failed. Please try again.');
            }
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
            'totalRaised' => $donations->sum(fn ($donation) => (float) $donation->amount)
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
            'campaign_id' => 'required|string',
            'message' => 'required|string|max:1000',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:8192',
            'images_uploaded_paths' => 'nullable|array',
            'images_uploaded_paths.*' => 'string|max:500',
        ]);

        $campaign = Campaign::findOrFail($request->campaign_id);

        // Check if user owns the campaign
        if ($campaign->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'error' => 'Unauthorized to update this campaign.'
            ], 403);
        }

        $imagePaths = $this->completedChunkPaths($request, 'images_uploaded_paths', 'campaign_update');

        // Handle multiple image uploads via R2
        if ($request->hasFile('images')) {
            try {
                foreach ($request->file('images') as $image) {
                    $imagePaths[] = $this->storage->upload($image, 'campaign_updates',
                        ['max_kb' => 8192, 'mimes' => config('r2.validation.image_mimes')]);
                }
            } catch (R2StorageException $e) {
                // Roll back any successfully uploaded files so we don't leak orphans.
                foreach ($imagePaths as $orphan) {
                    $this->storage->delete($orphan);
                }
                return response()->json(['success' => false, 'error' => 'File upload failed. Please try again.'], 422);
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

        // Resolve image keys to full URLs so the frontend can render them directly.
        $updatePayload = $update->toArray();
        $updatePayload['images'] = collect($update->images ?? [])
            ->map(fn ($key) => file_url($key))
            ->filter()
            ->values()
            ->all();

        return response()->json([
            'success' => true,
            'message' => 'Update posted successfully!',
            'update' => $updatePayload,
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
        if ((string) $donation->user_id !== (string) Auth::id()) {
            return response()->json(['success' => false, 'error' => 'Unauthorized'], 403);
        }

        if ($donation->status !== 'Scheduled') {
            return response()->json([
                'success' => false,
                'error' => 'Only scheduled donations can be cancelled.',
            ], 422);
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
        if ((string) $donation->user_id !== (string) Auth::id()) {
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
        $impactReports = ImpactReport::orderBy('report_date', 'desc') // Limit to 3 recent reports
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

    private function completedChunkPath(Request $request, string $input, string $module): ?string
    {
        $path = trim((string) $request->input($input, ''));
        if ($path === '' || ! $request->user()) {
            return null;
        }

        return $this->chunkUploads->completedPathForUser($path, $module, (string) $request->user()->getAuthIdentifier());
    }

    private function completedChunkPaths(Request $request, string $input, string $module): array
    {
        if (! $request->user()) {
            return [];
        }

        return collect((array) $request->input($input, []))
            ->map(fn ($path) => $this->chunkUploads->completedPathForUser(trim((string) $path), $module, (string) $request->user()->getAuthIdentifier()))
            ->filter()
            ->values()
            ->all();
    }
}
