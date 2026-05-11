<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Models\CampaignView;
use Illuminate\Http\Request;
use App\Models\Campaign;
use App\Jobs\PublishCampaignJob;
use App\Jobs\EndCampaignJob;
use App\Jobs\PublishRecurringCampaignJob;
use Livewire\Livewire;
use App\Models\Donation;
use Illuminate\Support\Str;
use App\Notifications\NewDonationNotification;
use App\Services\Storage\R2StorageService;
use App\Services\Storage\R2StorageException;


class CampaignController
{
    protected R2StorageService $storage;

    public function __construct(R2StorageService $storage)
    {
        $this->storage = $storage;
    }

    protected function noCacheView($view, $data = [])
    {
        $response = response()->view($view, $data);

        return $response->header('Cache-Control', 'no-cache, no-store, must-revalidate')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }

    public function campaignPage()
    {
        return $this->noCacheView('campaign.campaignpage');
    }

    public function createpage()
    {
        return view('campaign.campaigncreate');
    }

    public function createcampaign(Request $request)
    {
        $request->validate([
            'title'           => 'required|string|max:255',
            'description'     => 'required|string',
            'campaign_organizer' => 'required|string|max:30',
            'target_amount'   => 'required|numeric|min:1',
            'starts_at'       => 'nullable|date',
            'ends_at'         => 'nullable|date|after_or_equal:starts_at',
            'schedule_type'   => 'required|in:one_time,recurring',
            'recurring_days'  => 'nullable|array|required_if:schedule_type,recurring',
            'recurring_days.*' => 'string',
            'recurring_time'  => 'nullable|required_if:schedule_type,recurring|date_format:H:i',
            'featured_image'  => 'required|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'qr_code'         => 'required|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'gcash_number'    => 'required|string|regex:/^09[0-9]{9}$/|max:11',
            'images.*'        => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',

        ]);

        // Upload images to Cloudflare R2. If any upload fails we clean up any
        // objects we already wrote so R2 never retains orphaned files.
        $uploadedKeys = [];
        try {
            $featuredImagePath = $request->hasFile('featured_image')
                ? tap($this->storage->upload($request->file('featured_image'), 'campaign_featured',
                    ['max_kb' => 2048, 'mimes' => config('r2.validation.image_mimes')]),
                    function ($k) use (&$uploadedKeys) { $uploadedKeys[] = $k; })
                : null;

            $qrCodePath = $request->hasFile('qr_code')
                ? tap($this->storage->upload($request->file('qr_code'), 'campaign_qr',
                    ['max_kb' => 2048, 'mimes' => config('r2.validation.image_mimes')]),
                    function ($k) use (&$uploadedKeys) { $uploadedKeys[] = $k; })
                : null;

            $additionalImages = [];
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $key = $this->storage->upload($image, 'campaign_images',
                        ['max_kb' => 2048, 'mimes' => config('r2.validation.image_mimes')]);
                    $additionalImages[] = $key;
                    $uploadedKeys[] = $key;
                }
            }
        } catch (R2StorageException $e) {
            foreach ($uploadedKeys as $k) { $this->storage->delete($k); }
            return back()->withInput()->with('error', $e->getMessage());
        }

        // Determine initial status
        if ($request->input('schedule_type') === 'recurring') {
            $status = 'scheduled'; // Recurring campaigns start as scheduled
        } else {
            $status = $this->determineCampaignStatus(
                $request->input('starts_at'),
                $request->input('ends_at')
            );
        }

        // Create campaign
        $campaign = Campaign::create([
            'user_id'         => Auth::id(),
            'title'           => $request->input('title'),
            'description'     => $request->input('description'),
            'campaign_organizer' => $request->input('campaign_organizer'),
            'target_amount'   => $request->input('target_amount'),
            'current_amount'  => 0,
            'status'          => $status,
            'schedule_type'   => $request->input('schedule_type'),
            'recurring_days'  => $request->input('recurring_days') ?? [],
            'recurring_time'  => $request->input('recurring_time'),
            'starts_at'       => $request->input('starts_at'),
            'ends_at'         => $request->input('ends_at'),
            'featured_image'  => $featuredImagePath,
            'qr_code'        => $qrCodePath,
            'gcash_number'    => $request->input('gcash_number'),
            'images'          => $additionalImages,
            'views'           => 0,
            'donor_count'     => 0,
        ]);



        // One-time schedule handling
        if ($request->input('schedule_type') === 'one_time') {
            // Publish job (if scheduled for future)
            if ($status === 'scheduled' && $request->input('starts_at')) {
                $startTime = Carbon::parse($request->input('starts_at'));
                PublishCampaignJob::dispatch($campaign->campaign_id)->delay($startTime);
            }

            // End job (if end date provided)
            if ($request->input('ends_at')) {
                $endTime = Carbon::parse($request->input('ends_at'));
                if ($endTime->isFuture()) {
                    EndCampaignJob::dispatch($campaign->campaign_id)->delay($endTime);
                }
            }
        }

        // Recurring schedule handling
        if ($request->input('schedule_type') === 'recurring') {
            $recurringDays = $request->input('recurring_days') ?? [];
            $recurringTime = $request->input('recurring_time');

            foreach ($recurringDays as $day) {
                $now = Carbon::now();
                $scheduled = Carbon::parse("this $day")->setTimeFromTimeString($recurringTime);

                // If "this $day" has already passed, move to next week
                if ($scheduled->isPast()) {
                    $scheduled = Carbon::parse("next $day")->setTimeFromTimeString($recurringTime);
                }

                PublishRecurringCampaignJob::dispatch(
                    $campaign->campaign_id,
                    $day,
                    $recurringTime
                )->delay($scheduled);
            }
        }

        return redirect()->route('campaignpage')->with('success');
    }

    //   Determine campaign status based on start date
    private function determineCampaignStatus($startDate, $endDate)
    {
        $now = Carbon::now();

        // Check if campaign has already ended
        if (!empty($endDate)) {
            $endTime = Carbon::parse($endDate);
            if ($endTime->isPast()) {
                return 'ended';
            }
        }

        // If no start date provided, publish immediately
        if (empty($startDate)) {
            return 'active';
        }

        $startTime = Carbon::parse($startDate);

        if ($startTime->isFuture()) {
            return 'scheduled';
        } else {
            return 'active';
        }
    }

    public function campaignview($id, Request $request)
    {
        $campaign = Campaign::with([
            'donations' => function ($q) {
                $q->select(
                    'donation_id',
                    'campaign_id',
                    'user_id',
                    'donor_name',
                    'amount',
                    'reference_number',
                    'proof_image',
                    'status',
                    'created_at'
                )
                    ->latest();
            },
            'updates' => function ($q) {
                $q->with('organizer') // Load the organizer relationship
                    ->latest(); // Get updates in descending order (newest first)
            }
        ])->findOrFail($id);

        $user = Auth::user();
        $userId = $user ? $user->user_id : null;
        $ip = $request->ip();
        $agent = substr($request->userAgent(), 0, 255);

        // Check if this visitor already viewed this campaign
        $exists = CampaignView::where('campaign_id', $id)
            ->where('user_id', $userId)
            ->where('ip_address', $ip)
            ->where('user_agent', $agent)
            ->exists();

        if (!$exists) {
            CampaignView::create([
                'campaign_id' => $id,
                'user_id'     => $userId,
                'ip_address'  => $ip,
                'user_agent'  => $agent,
            ]);

            $campaign->views = ((int) ($campaign->views ?? 0)) + 1;
            $campaign->save();
        }

        return $this->noCacheView('campaign.campaignview', compact('campaign'));
    }

    // Campaignview Donaton COntroller
    public function donate(Request $request)
    {
        if ($request->ajax() && $request->has('reference_number')) {
            $ref = Str::upper(preg_replace('/\s+/', '', $request->input('reference_number')));
            $exists = Donation::where('reference_number', $ref)->exists();
            return response()->json(['exists' => $exists]);
        }

        $rules = [
            'campaign_id'      => 'required|string',
            'amount'           => 'required|numeric|min:1',
            'reference_number' => 'required|string|max:100',
            'proof_image'      => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
        ];

        if (!Auth::check()) {
            $rules['donor_name']  = 'required|string|max:191';
            $rules['donor_email'] = 'nullable|email';
        }

        $validated = $request->validate($rules);

        $ref = Str::upper(preg_replace('/\s+/', '', $validated['reference_number']));

        if (Donation::where('reference_number', $ref)->exists()) {
            return back()->withInput()->with('error', 'That GCash reference number has already been used.');
        }

        try {
            $proofPath = $this->storage->upload($request->file('proof_image'), 'donation_proofs',
                ['max_kb' => 2048, 'mimes' => ['image/jpeg', 'image/png', 'image/webp']]);
        } catch (R2StorageException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }

        $isAnonymous = $request->boolean('is_anonymous');
        $donorName = $donorEmail = null;

        if (!$isAnonymous) {
            if (Auth::check()) {
                $donorName  = Auth::user()->first_name ?? Auth::user()->name;
                $donorEmail = Auth::user()->email;
            } else {
                $donorName  = $validated['donor_name'] ?? null;
                $donorEmail = $validated['donor_email'] ?? null;
            }
        }

        // Create donation
        $donation = Donation::create([
            'campaign_id'      => $validated['campaign_id'],
            'user_id'          => ($isAnonymous ? null : Auth::id()),
            'donor_name'       => $donorName,
            'donor_email'      => $donorEmail,
            'is_anonymous'     => $isAnonymous,
            'amount'           => $validated['amount'],
            'reference_number' => $ref,
            'proof_image'      => $proofPath,
            'message_code'     => "CAMP-" . $validated['campaign_id'],
            'status'           => 'pending',
        ]);

        // Update campaign stats
        $campaign = Campaign::findOrFail($validated['campaign_id']);
        $campaign->adjustDonationStats((float) $validated['amount'], 1);
        $campaign->organizer->notify(new NewDonationNotification($donation, $campaign));





        return redirect()->back()->with('success', 'Donation submitted successfully! Thank you for your support.');
    }


    public function deleteAll(Request $request)
    {
        try {
            Auth::user()->notifications()->delete();

            return response()->json([
                'success' => true,
                'message' => 'All notifications deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete notifications'
            ], 500);
        }
    }

    public function deleteSelected(Request $request)
    {
        try {
            $notificationIds = $request->input('notification_ids', []);

            if (empty($notificationIds)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No notifications selected'
                ], 400);
            }

            Auth::user()->notifications()->whereIn('id', $notificationIds)->delete();

            return response()->json([
                'success' => true,
                'message' => 'Selected notifications deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete selected notifications'
            ], 500);
        }
    }

    public function delete($id)
    {
        try {
            Auth::user()->notifications()->where('id', $id)->delete();

            return response()->json([
                'success' => true,
                'message' => 'Notification deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete notification'
            ], 500);
        }
    }
}
