<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\VerificationRequest;
use App\Models\IdentityStatus;
use App\Models\DropOffPoint;
use App\Models\InKindDonation;
use App\Models\DncRecord;
use App\Models\Event;
use App\Models\EventRegistration;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Models\VolunteerRole;
use Illuminate\Support\Facades\Log;
use App\Models\Campaign;
use App\Models\ManualDonationRequest;
use App\Models\Donation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use App\Models\AdminAccount;
use App\Notifications\VerificationDecisionNotification;
use Dompdf\Dompdf;
use Dompdf\Options;
use App\Notifications\ManualDonationStatusNotification;
use App\Notifications\DonationDistributedNotification;
use App\Models\ImpactReport;


class AdministratorController
{

    // Session flushing method
    protected function noCacheView($view, $data = [])
    {
        $response = response()->view($view, $data);

        return $response->header('Cache-Control', 'no-cache, no-store, must-revalidate')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }

    public function showLoginForm()
    {
        // Check session instead of Auth::check()
        if (session('admin_logged_in')) {
            return redirect()->route('admin.home');
        }

        return view('administrator.admin-login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        // Check if login is username or email
        $loginType = filter_var($request->username, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        // Find admin in admin_accounts table
        $admin = \App\Models\AdminAccount::where($loginType, $request->username)->first();

        // Check if admin exists and password is correct
        if ($admin && \Illuminate\Support\Facades\Hash::check($request->password, $admin->password)) {
            // Store admin in session (separate from regular users)
            session([
                'admin_id' => $admin->admin_id,
                'admin_username' => $admin->username,
                'admin_email' => $admin->email,
                'admin_logged_in' => true,
            ]);

            $request->session()->regenerate();

            return redirect()->route('admin.home')
                ->with('success', 'Welcome back!');
        }

        return back()->withErrors([
            'username' => 'Invalid credentials.',
        ])->onlyInput('username');
    }

    public function dashboard(Request $request)
    {
        // Check if admin is logged in
        if (!$request->session()->has('admin_logged_in')) {
            return redirect()->route('admin.login');
        }

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

        return $this->noCacheView('administrator.admin-home', compact(
            'endedEvents',
            'impactReports',
            'endedCampaigns' // NEW
        ));
    }

    public function logout(Request $request)
    {
        // Clear admin session variables
        $request->session()->forget([
            'admin_id',
            'admin_username',
            'admin_email',
            'admin_logged_in',
        ]);



        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login')
            ->with('status', 'Logged out successfully.');
    }

    /*=========================Account Verification Controller ================================*/
    public function accountpage(Request $request)
    {

        // Check if admin is logged in
        if (!$request->session()->has('admin_logged_in')) {
            return redirect()->route('admin.login');
        }

        $status = $request->query('status');

        $requests = VerificationRequest::with('user')
            ->when($status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->orderBy('created_at', 'desc')
            ->get();

        $counts = [
            'pending'  => VerificationRequest::where('status', 'pending')->count(),
            'approved' => VerificationRequest::where('status', 'approved')->count(),
            'rejected' => VerificationRequest::where('status', 'rejected')->count(),
            'reupload' => VerificationRequest::where('status', 'reupload')->count(),
        ];

        return view('administrator.account.accountpage', compact('requests', 'status', 'counts'));
    }


    public function getAccountStats()
    {
        $counts = [
            'pending'  => VerificationRequest::where('status', 'pending')->count(),
            'approved' => VerificationRequest::where('status', 'approved')->count(),
            'rejected' => VerificationRequest::where('status', 'rejected')->count(),
            'reupload' => VerificationRequest::where('status', 'reupload')->count(),
            'total'    => VerificationRequest::count(),
        ];

        return response()->json([
            'success' => true,
            'stats' => $counts
        ]);
    }


    public function decision(Request $r)
    {
        $r->validate([
            'request_id'       => 'required|integer|exists:verification_requests,request_id',
            'action'           => 'required|in:approved,rejected,request_reupload',
            'notes'            => 'nullable|string|max:2000',
            'reupload_fields'  => 'nullable|array',
            'reupload_fields.*' => 'in:id_front,id_back,face_photo,selfie',
        ]);

        $req = VerificationRequest::findOrFail($r->request_id);

        if ($r->action === 'request_reupload') {
            $req->status = 'reupload';
            $req->reupload_fields = $r->reupload_fields ?? [];
        } else {
            $req->status = $r->action;
            $req->reupload_fields = null;
        }

        $req->review_notes = $r->notes;
        $req->save();

        IdentityStatus::updateOrCreate(
            ['user_id' => $req->user_id],
            ['status' => $req->status === 'approved' ? 'Verified'
                : ($req->status === 'rejected' ? 'Rejected'
                    : ($req->status === 'reupload' ? 'Reupload' : 'Pending'))]
        );

        // Send notification to the user
        if ($req->user) {
            $req->user->notify(new VerificationDecisionNotification($req, $r->action, $r->notes));
        }

        $message = match ($r->action) {
            'approved'         => 'Account successfully verified.',
            'rejected'         => 'Account has been rejected.',
            'request_reupload' => 'Request reupload has been sent.',
            default            => 'Action completed successfully.'
        };

        if ($r->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'status'  => $req->status,
            ]);
        }

        return back()->with('message', 'Decision recorded: ' . $r->action);
    }

    /*===========================================================================================*/

    /*==================================Campaign Controller===============================================*/

    public function campaignview(Request $request)
    {
        // Check if admin is logged in
        if (!$request->session()->has('admin_logged_in')) {
            return redirect()->route('admin.login');
        }

        $campaigns = Campaign::with([
            'organizer',
            'manualRequests.creator'
        ])->orderBy('created_at', 'desc')->get();

        return view('administrator.campaign.campaignpage', compact('campaigns'));
    }

    public function getCampaignStats()
    {
        // Existing stats
        $totalActiveCampaigns = Campaign::where('status', 'active')->count();
        $totalFundsRaised = Campaign::sum('current_amount');

        // New stats for Today's Donations card
        $todayDonations = Donation::whereDate('created_at', today())
            ->sum('amount');

        // Get yesterday's donations for comparison
        $yesterdayDonations = Donation::whereDate('created_at', today()->subDay())
            ->sum('amount');

        // Calculate percentage change for donations
        $donationChange = 0;
        if ($yesterdayDonations > 0) {
            $donationChange = (($todayDonations - $yesterdayDonations) / $yesterdayDonations) * 100;
        } elseif ($todayDonations > 0) {
            // If no donations yesterday but donations today, show 100% increase
            $donationChange = 100;
        }

        // New stats for Campaign Count card
        $totalCampaigns = Campaign::count();

        // Get total campaigns from yesterday for comparison
        $yesterdayCampaigns = Campaign::whereDate('created_at', '<', today())
            ->count();

        // Calculate percentage change for campaigns
        $campaignChange = 0;
        if ($yesterdayCampaigns > 0) {
            $campaignChange = (($totalCampaigns - $yesterdayCampaigns) / $yesterdayCampaigns) * 100;
        } elseif ($totalCampaigns > 0) {
            // If no campaigns yesterday but campaigns today, show 100% increase
            $campaignChange = 100;
        }

        return response()->json([
            'totalActiveCampaigns' => $totalActiveCampaigns,
            'totalFundsRaised' => $totalFundsRaised,
            'todayDonations' => $todayDonations,
            'totalCampaigns' => $totalCampaigns,
            'donationChange' => round($donationChange, 1),
            'campaignChange' => round($campaignChange, 1),
        ]);
    }

    public function getMonthlyFunds()
    {
        // Get current year
        $currentYear = date('Y');

        // Initialize array for all months
        $monthlyData = [];

        for ($month = 1; $month <= 12; $month++) {
            // Sum donations for each month in current year
            $monthlyData[] = Donation::whereYear('created_at', $currentYear)
                ->whereMonth('created_at', $month)
                ->sum('amount');
        }

        return response()->json([
            'monthlyData' => $monthlyData,
            'year' => $currentYear
        ]);
    }


    public function getLatestCampaigns(Request $request)
    {
        $query = Campaign::with(['manualRequests.creator', 'donations']);

        // Search functionality
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('campaign_id', 'like', "%{$search}%");
            });
        }

        // Status filter - updated for new options
        if ($request->has('status') && $request->status !== 'all') {
            $now = now();

            switch ($request->status) {
                case 'active':
                    $query->where('status', 'active')
                        ->where('starts_at', '<=', $now)
                        ->where(function ($q) use ($now) {
                            $q->where('ends_at', '>', $now)
                                ->orWhereNull('ends_at');
                        });
                    break;

                case 'scheduled':
                    // Campaigns that haven't started yet
                    $query->where('status', 'scheduled')
                        ->where('starts_at', '>', $now);
                    break;

                case 'ended':
                    // Campaigns with 'ended' status OR past their end date
                    $query->where(function ($q) use ($now) {
                        $q->where('status', 'ended')
                            ->orWhere('ends_at', '<', $now);
                    });
                    break;

                default:
                    // For any other status
                    $query->where('status', $request->status);
                    break;
            }
        }

        // Order and paginate
        $campaigns = $query->orderBy('created_at', 'desc')->paginate(10);

        $latestId = $campaigns->first()->campaign_id ?? 0;
        $html = view('administrator.campaign.partials.campaign_list', compact('campaigns'))->render();

        return response()->json([
            'html' => $html,
            'latest_id' => $latestId,
            'pagination' => [
                'current_page' => $campaigns->currentPage(),
                'last_page' => $campaigns->lastPage(),
                'per_page' => $campaigns->perPage(),
                'total' => $campaigns->total(),
                'next_page_url' => $campaigns->nextPageUrl(),
                'prev_page_url' => $campaigns->previousPageUrl(),
            ]
        ]);
    }


    public function approveManual($id)
    {
        $request = ManualDonationRequest::with(['creator', 'campaign'])->findOrFail($id);

        if ($request->status !== 'pending') {
            return response()->json(['message' => 'Already processed'], 400);
        }

        Donation::create([
            'campaign_id'      => $request->campaign_id,
            'user_id'          => null,
            'is_anonymous'     => true,
            'donor_name'       => null,
            'donor_email'      => null,
            'amount'           => $request->amount,
            'reference_number' => $request->reference_number,
            'proof_image'      => $request->proof_image,
            'status'           => 'verified',
        ]);

        $campaign = Campaign::find($request->campaign_id);
        $campaign->increment('current_amount', $request->amount);
        $campaign->increment('donor_count');

        $request->update([
            'status' => 'approved',
            'approved_by' => Auth::id(),
            'reviewed_at' => now(),
        ]);


        if ($request->creator) {
            $request->creator->notify(
                new ManualDonationStatusNotification(
                    $request,
                    'approved',
                    Auth::user()->name ?? 'Administrator'
                )
            );
        }

        return response()->json(['message' => 'Manual donation approved successfully.']);
    }


    public function rejectManual($id)
    {
        $request = ManualDonationRequest::with(['creator', 'campaign'])->findOrFail($id);

        if ($request->status !== 'pending') {
            return response()->json(['message' => 'Already processed'], 400);
        }

        $request->update([
            'status' => 'rejected',
            'approved_by' => Auth::id(),
            'reviewed_at' => now(),
        ]);


        if ($request->creator) {
            $request->creator->notify(
                new ManualDonationStatusNotification(
                    $request,
                    'rejected',
                    Auth::user()->name ?? 'Administrator'
                )
            );
        }

        return response()->json(['message' => 'Manual donation request rejected.']);
    }



    public function exportCampaignPdf($campaignId)
    {
        $campaign = Campaign::with(['manualRequests', 'manualRequests.creator'])
            ->findOrFail($campaignId);

        // Calculate statistics
        $totalRequests = $campaign->manualRequests->count();
        $approvedRequests = $campaign->manualRequests->where('status', 'approved')->count();
        $pendingRequests = $campaign->manualRequests->where('status', 'pending')->count();
        $rejectedRequests = $campaign->manualRequests->where('status', 'rejected')->count();
        $totalAmount = $campaign->manualRequests->sum('amount');
        $approvedAmount = $campaign->manualRequests->where('status', 'approved')->sum('amount');

        $data = [
            'campaign' => $campaign,
            'totalRequests' => $totalRequests,
            'approvedRequests' => $approvedRequests,
            'pendingRequests' => $pendingRequests,
            'rejectedRequests' => $rejectedRequests,
            'totalAmount' => $totalAmount,
            'approvedAmount' => $approvedAmount,
            'date' => now()->format('F d, Y'),
        ];

        // Configure DomPDF options
        $options = new Options();
        $options->set('isRemoteEnabled', true);
        $options->set('isHtml5ParserEnabled', true);
        $options->set('defaultFont', 'Arial');

        $dompdf = new Dompdf($options);

        // Load HTML content
        $html = view('administrator.campaign.export.campaign-report', $data)->render();
        $dompdf->loadHtml($html);

        // Set paper size and orientation
        $dompdf->setPaper('A4', 'landscape');

        // Render PDF
        $dompdf->render();

        $filename = "Manual-Donations-" . str_replace(' ', '-', $campaign->title) . "-" . now()->format('M-d-Y') . ".pdf";

        // Return PDF as download
        return $dompdf->stream($filename);
    }
    /*=========================================================================================*/


    /*=========================In-Kind Donation (DROPOFF) Controller ================================*/

    public function admininkindpage(Request $request)
    {

        // Check if admin is logged in
        if (!$request->session()->has('admin_logged_in')) {
            return redirect()->route('admin.login');
        }

        $totalItems = InKindDonation::where('status', 'Received')->sum('quantity');
        $upcoming = InKindDonation::where('status', 'Scheduled')->count();


        $dropoffs = DropOffPoint::orderBy('created_at', 'desc')->get();
        $donations = InKindDonation::with(['dropOffPoint', 'user'])->get();

        return view('administrator.inkind.inkindpage', compact('dropoffs', 'donations', 'totalItems', 'upcoming'));
    }

    public function addlocation(Request $request)
    {
        $request->validate([
            'name'              => 'required|string|max:255',
            'address'           => 'required|string|max:500',
            'schedule_datetime' => 'required|string|max:255',
            'latitude'          => 'nullable|numeric|between:-90,90',
            'longitude'         => 'nullable|numeric|between:-180,180',
        ]);

        $location = DropOffPoint::create([
            'name'              => $request->name,
            'address'           => $request->address,
            'schedule_datetime' => $request->schedule_datetime,
            'latitude'          => $request->latitude,
            'longitude'         => $request->longitude,
            'is_active'         => true, // default active
        ]);

        return response()->json([
            'created' => $location,
            'stats'   => [
                'total'  => DropOffPoint::count(),
                'active' => DropOffPoint::where('is_active', true)->count(),
            ],
        ]);
    }
    // ---------- UPDATE ----------
    public function updatelocation(Request $request, $dropoff_id)
    {
        $request->validate([
            'name'              => 'required|string|max:255',
            'address'           => 'required|string|max:255',
            'schedule_datetime' => 'required|string|max:255',
            'latitude'          => 'nullable|numeric|between:-90,90',
            'longitude'         => 'nullable|numeric|between:-180,180',
        ]);

        $location = DropOffPoint::findOrFail($dropoff_id);

        $location->update([
            'name'              => $request->name,
            'address'           => $request->address,
            'schedule_datetime' => $request->schedule_datetime,
            'latitude'          => $request->latitude,
            'longitude'         => $request->longitude,

        ]);

        return response()->json([
            'updated' => $location,
            'stats'   => [
                'total'  => DropOffPoint::count(),
                'active' => DropOffPoint::where('is_active', true)->count(),
            ],
        ]);
    }

    // ---------- DELETE ----------
    public function deletelocation($dropoff_id)
    {
        $location = DropOffPoint::findOrFail($dropoff_id);
        $location->delete();

        return response()->json([
            'deleted' => $dropoff_id,
            'stats'   => [
                'total'  => DropOffPoint::count(),
                'active' => DropOffPoint::where('is_active', true)->count(),
            ],
        ]);
    }

    // ---------- TOGGLE ACTIVE ----------
    public function togglelocation($dropoff_id)
    {
        $location = DropOffPoint::findOrFail($dropoff_id);
        $location->is_active = !$location->is_active;
        $location->save();

        return response()->json([
            'updated' => $location,
            'stats'   => [
                'total'  => DropOffPoint::count(),
                'active' => DropOffPoint::where('is_active', true)->count(),
            ],
        ]);
    }

    /*============================In-Kind Donation (IN_KINDS) Controller ================================*/

    public function updatestatus(Request $request)
    {

        $donation = InKindDonation::findOrFail($request->id);
        $donation->status = $request->status;
        $donation->save();

        return response()->json([
            'success' => true,
            'new_status' => $donation->status
        ]);
    }

    // Update donation info from modal
    public function updatemodal(Request $request, $id)
    {
        $donation = InKindDonation::findOrFail($id);

        $request->validate([
            'donor_name'   => 'nullable|string|max:255',
            'donor_email'  => 'nullable|email|max:255',
            'donor_phone'  => 'nullable|string|max:20',
            'item_name'    => 'required|string|max:255',
            'category'     => 'required|string|max:255',
            'quantity'     => 'required|integer|min:1',
        ]);

        $donation->update($request->only([
            'donor_name',
            'donor_email',
            'donor_phone',
            'item_name',
            'category',
            'quantity',
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Donation updated successfully',
            'donation' => $donation
        ]);
    }

    public function getLatestDonations()
    {
        $donations = InKindDonation::with('dropOffPoint')
            ->orderBy('created_at', 'desc')
            ->get();

        // UPDATE THIS LINE: Include both Received AND Distributed
        $receivedTotalItems = InKindDonation::whereIn('status', ['Received', 'Distributed'])->sum('quantity');

        $upcoming = InKindDonation::where('status', 'Scheduled')->count();
        $receivedCount = InKindDonation::where('status', 'Received')->count();

        // UPDATE THIS LINE: Include both Received AND Distributed for last month comparison
        $lastMonthTotal = InKindDonation::whereIn('status', ['Received', 'Distributed'])
            ->whereMonth('created_at', now()->subMonth()->month)
            ->whereYear('created_at', now()->subMonth()->year)
            ->sum('quantity');

        $percentageChange = $lastMonthTotal > 0
            ? round((($receivedTotalItems - $lastMonthTotal) / $lastMonthTotal) * 100, 2)
            : 0;

        return response()->json([
            'success' => true,
            'donations' => $donations,
            'count' => $donations->count(),
            'stats' => [
                'total_items' => $receivedTotalItems, // This now includes both Received and Distributed
                'upcoming' => $upcoming,
                'percentage' => $percentageChange,
            ]
        ]);
    }

    public function getInKindChartData()
    {

        $data = InKindDonation::selectRaw('
            MONTH(created_at) as month,
            SUM(quantity) as total_items
        ')
            ->whereIn('status', ['Received', 'Distributed'])
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }


    public function getCategoryChartData()
    {
        $data = InKindDonation::selectRaw('category, SUM(quantity) as total_items')
            ->whereIn('status', ['Received', 'Distributed'])
            ->groupBy('category')
            ->orderByDesc('total_items')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }


    public function impactreportstore(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'report_date' => 'required|date',
            'description' => 'required|string',
            'selected_donations' => 'required|array',
            'selected_donations.*' => 'exists:in_kind_donations,inkind_id',
            'photos.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        try {
            // Upload photos
            $photoPaths = [];
            if ($request->hasFile('photos')) {
                foreach ($request->file('photos') as $photo) {
                    $path = $photo->store('impact-reports/photos', 'public');
                    $photoPaths[] = $path;
                }
            }

            // Create impact report
            $impactReport = ImpactReport::create([
                'title' => $request->title,
                'description' => $request->description,
                'report_date' => $request->report_date,
                'photos' => $photoPaths
            ]);

            // Attach donations to the impact report
            $impactReport->donations()->attach($request->selected_donations);

            // Get all donations that will be marked as distributed
            $donations = InKindDonation::whereIn('inkind_id', $request->selected_donations)
                ->with('user') // Eager load user relationships
                ->get();

            // Group donations by user_id (only for registered users)
            $donationsByUser = [];

            foreach ($donations as $donation) {
                // Only process donations from registered users
                if ($donation->user_id && $donation->user) {
                    $userId = $donation->user_id;

                    if (!isset($donationsByUser[$userId])) {
                        $donationsByUser[$userId] = [
                            'user' => $donation->user,
                            'donations' => [],
                        ];
                    }

                    $donationsByUser[$userId]['donations'][] = $donation;
                }

                // Update status for ALL donations (including guest donations)
                $donation->update(['status' => 'Distributed']);
            }

            // Send notifications to registered users
            foreach ($donationsByUser as $userData) {
                $userDonations = collect($userData['donations']);

                if ($userDonations->count() > 0 && $userData['user']) {
                    $userData['user']->notify(new DonationDistributedNotification($userDonations, $impactReport));
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Impact report created successfully!',
                'data' => $impactReport->load('donations')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }



    public function getReceivedDonations(Request $request)
    {
        try {
            // Get only received donations
            $donations = InKindDonation::with(['user', 'dropOffPoint'])
                ->where('status', 'Received')
                ->orderBy('created_at', 'desc')
                ->get();

            // Return the rendered partial view
            $html = view('administrator.inkind.partials.impact-donations', [
                'donations' => $donations
            ])->render();

            return response()->json([
                'success' => true,
                'donations' => $donations,
                'html' => $html
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load donations'
            ], 500);
        }
    }


    // In your controller
    public function paginateDonations(Request $request)
    {
        try {
            $perPage = $request->per_page ?? 10;

            $query = InKindDonation::with(['user', 'dropOffPoint'])
                ->orderBy('created_at', 'desc');

            // Apply search filter
            if ($request->search) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('donor_name', 'LIKE', "%{$search}%")
                        ->orWhere('item_name', 'LIKE', "%{$search}%")
                        ->orWhere('category', 'LIKE', "%{$search}%")
                        ->orWhereHas('user', function ($userQuery) use ($search) {
                            $userQuery->where('email', 'LIKE', "%{$search}%");
                        });
                });
            }

            // Apply status filter
            if ($request->status && $request->status !== 'all') {
                $query->where('status', $request->status);
            }

            // Apply date filter
            if ($request->date) {
                $query->whereDate('created_at', $request->date);
            }

            $donations = $query->paginate($perPage);

            // Format donations for response
            $formattedDonations = $donations->map(function ($donation) {
                return [
                    'id' => $donation->inkind_id,
                    'donor_name' => $donation->donor_name,
                    'donor_email' => $donation->donor_email,
                    'donor_phone' => $donation->donor_phone,
                    'item_name' => $donation->item_name,
                    'category' => $donation->category,
                    'quantity' => $donation->quantity,
                    'dropoff_name' => $donation->dropOffPoint->name ?? null,
                    'status' => $donation->status,
                    'created_at' => $donation->created_at->format('Y-m-d'),
                    'avatar_url' => optional($donation->user)->profile_photo_url ?? null,
                ];
            });

            return response()->json([
                'success' => true,
                'donations' => $formattedDonations,
                'pagination' => [
                    'current_page' => $donations->currentPage(),
                    'last_page' => $donations->lastPage(),
                    'per_page' => $donations->perPage(),
                    'total' => $donations->total(),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading donations: ' . $e->getMessage()
            ], 500);
        }
    }

    /*========================================Event Controller ========================================*/

    public function createevent()
    {
        return view('administrator.event.event-create');
    }

    public function submitevent(Request $request)
    {

        $request->validate([
            'title'               => 'required|string|max:255',
            'description'         => 'required|string',
            'photo'               => 'nullable|image|mimes:jpg,jpeg,png|max:5120', // 5MB
            'start_date'          => 'required|date',
            'end_date'            => 'required|date|after_or_equal:start_date',
            'location'            => 'required|string|max:255',
            'deadline'            => 'required|date|before:start_date',
            'coordinator_name'    => 'required|string|max:255',
            'coordinator_email'   => 'required|email',
            'coordinator_phone'   => 'required|string|max:20',
            'roles'               => 'required|array|min:1',
            'roles.*.name'        => 'required|string|max:255',
            'roles.*.description' => 'required|string',
        ]);


        $photoPath = $request->hasFile('photo')
            ? $request->file('photo')->store('event_photos', 'public')
            : null;


        $event = Event::create([
            'title'             => $request->input('title'),
            'description'       => $request->input('description'),
            'photo'             => $photoPath,
            'start_date'        => $request->input('start_date'),
            'end_date'          => $request->input('end_date'),
            'location'          => $request->input('location'),
            'deadline'          => $request->input('deadline'),
            'coordinator_name'  => $request->input('coordinator_name'),
            'coordinator_email' => $request->input('coordinator_email'),
            'coordinator_phone' => $request->input('coordinator_phone'),
        ]);


        foreach ($request->input('roles') as $role) {
            VolunteerRole::create([
                'event_id'    => $event->event_id,
                'name'        => $role['name'],
                'description' => $role['description'],
            ]);
        }


        return redirect()->route('adminevent.page')
            ->with('message', 'Event created successfully!');
    }

    public function eventview(Request $request)
    {

        // Check if admin is logged in
        if (!$request->session()->has('admin_logged_in')) {
            return redirect()->route('admin.login');
        }

        $events = Event::with(['registrations.user'])->get();


        $registrations = EventRegistration::with('event')->get();


        $volunteers = $registrations->groupBy('email')->map(function ($group) {
            return [
                'first_name' => $group->first()->first_name,
                'last_name'  => $group->first()->last_name,
                'email'      => $group->first()->email,
                'attended'   => $group->where('status', 'attended')->count(),
                'missed'     => $group->where('status', 'absent')->count(),
                'total'      => $group->count(),
                'recent'     => $group->sortByDesc(fn($r) => $r->event->start_date)->take(5),
            ];
        });


        return $this->noCacheView('administrator.event.eventpage', compact('events', 'volunteers'));
    }

    //event cards live
    public function live()
    {
        $events = Event::with('registrations')->get();
        return view('administrator.event.partials.event_list', compact('events'))->render();
    }

    //statistics
    public function getEventStatistics()
    {
        $now = Carbon::now();


        $available = Event::where('start_date', '>', $now)->count();

        $ongoing = Event::where('start_date', '<=', $now)
            ->where('end_date', '>=', $now)
            ->count();

        $ended = Event::where('end_date', '<', $now)->count();


        return response()->json([
            'available' => $available,
            'ongoing'   => $ongoing,
            'ended'     => $ended,
        ]);
    }

    //volunteer management
    public function volunteerdetails()
    {
        $registrations = EventRegistration::with('event')->get();

        $volunteers = $registrations->groupBy('email')->map(function ($group) {
            return [
                'first_name' => $group->first()->first_name,
                'last_name'  => $group->first()->last_name,
                'email'      => $group->first()->email,
                'attended'   => $group->where('status', 'attended')->count(),
                'missed'     => $group->where('status', 'absent')->count(),
                'total'      => $group->count(),
                'recent'     => $group->sortByDesc(fn($r) => $r->event->start_date)->take(5),
            ];
        });

        return view('administrator.event.eventpage', [
            'volunteers' => $volunteers,
        ]);
    }

    // Volunteer detail (AJAX fetch)
    public function showvolunteer($email)
    {
        $registrations = EventRegistration::with('event')
            ->where('email', $email)
            ->orderByDesc('event_id')
            ->take(5)
            ->get();

        return response()->json($registrations);
    }

    // Volunteer Status Update
    public function updateVolunteerStatus(Request $request, $registration_id)
    {
        $registration = EventRegistration::findOrFail($registration_id);
        $registration->status = $request->status;
        $registration->save();

        return response()->json(['success' => true]);
    }

    public function volunteerLive()
    {
        $registrations = EventRegistration::with('event')->get();

        $volunteers = $registrations->groupBy('email')->map(function ($group) {
            return [
                'first_name' => $group->first()->first_name,
                'last_name'  => $group->first()->last_name,
                'email'      => $group->first()->email,
                'attended'   => $group->where('status', 'attended')->count(),
                'missed'     => $group->where('status', 'absent')->count(),
                'total'      => $group->count(),
                'recent'     => $group->sortByDesc(fn($r) => $r->event->start_date)->take(5),
            ];
        });


        return view('administrator.event.partials.volunteer_list', compact('volunteers'))->render();
    }

    // Volunterchart
    public function getVolunteerParticipationData(): JsonResponse
    {
        // Fetch each event with its volunteer registration count
        $data = Event::withCount('registrations')
            ->orderBy('start_date', 'asc')
            ->get(['title', 'event_id']);

        return response()->json([
            'labels' => $data->pluck('title'),
            'values' => $data->pluck('registrations_count'),
        ]);
    }

    public function volunteerData(Request $request)
    {
        $eventId = $request->get('event_id', 'all');

        if ($eventId !== 'all') {
            $event = Event::with('registrations')->find($eventId);
            if (!$event) return response()->json(['labels' => [], 'values' => [], 'eventTitle' => 'Not found']);

            // Group by status
            $labels = ['Attended', 'Absent', 'Registered'];
            $values = [
                $event->registrations->where('status', 'attended')->count(),
                $event->registrations->where('status', 'absent')->count(),
                $event->registrations->where('status', 'registered')->count(),
            ];

            return response()->json([
                'labels' => $labels,
                'values' => $values,
                'eventTitle' => $event->title
            ]);
        }


        $events = Event::withCount(['registrations as attendees' => function ($q) {
            $q->where('status', 'attended');
        }])->get();

        return response()->json([
            'labels' => $events->pluck('title'),
            'values' => $events->pluck('attendees'),
            'eventTitle' => null
        ]);
    }

    public function volunteerProfile($email)
    {
        $registration = EventRegistration::where('email', $email)->first();

        if (!$registration) {
            return response()->json(['success' => false]);
        }

        return response()->json([
            'success' => true,
            'volunteer' => [
                'first_name'     => $registration->first_name,
                'last_name'      => $registration->last_name,
                'email'          => $registration->email,
                'phone'          => $registration->phone,
                'messenger_link' => $registration->messenger_link,
                'age'            => $registration->age,
                'sex'            => $registration->sex,
                'address'        => $registration->address,
            ]
        ]);
    }


    /*========================================================================================================*/

    // ===========================================DNC CONTROLLER =================================================

    public function dncview(Request $request)
    {

        // Check if admin is logged in
        if (!$request->session()->has('admin_logged_in')) {
            return redirect()->route('admin.login');
        }

        $records = DncRecord::latest()->paginate(12);

        return view('administrator.dnc.dncrecordspage', compact('records'));
    }

    public function dncadd()
    {
        return view('administrator.dnc.adddnc');
    }


    public function dncstore(Request $request)
    {
        try {

            $validated = $request->validate([
                // A. General
                'date'             => 'required|date',
                'assessor'         => 'nullable|string|max:255',
                'event'            => 'required|string|max:255',
                'province'         => 'required|string|max:255',
                'municipality'     => 'required|string|max:255',
                'barangay'         => 'required|string|max:255',
                'households'       => 'nullable|integer',
                'individuals'      => 'nullable|integer',

                // Population
                'pop_male'         => 'nullable|integer',
                'pop_female'       => 'nullable|integer',
                'pop_children'     => 'nullable|integer',
                'pop_elderly'      => 'nullable|integer',
                'pop_pwds'         => 'nullable|integer',

                // B. Damage
                'houses_full'      => 'nullable|integer',
                'houses_partial'   => 'nullable|integer',
                'infrastructure'   => 'nullable|array',

                // Livelihood
                'crop_type'        => 'nullable|string|max:255',
                'crop_hectares'    => 'nullable|numeric',
                'livestock_type'   => 'nullable|string|max:255',
                'livestock_number' => 'nullable|integer',
                'tools_destroyed'  => 'nullable|string|max:255',

                // Community Facilities
                'facilities_affected' => 'nullable|in:Yes,No',
                'facilities_notes'    => 'nullable|string',

                // C. Needs
                'needs'            => 'nullable|array',
                'needs_other'      => 'nullable|string',

                // D. Capacities
                'groups'           => 'nullable|string|max:255',
                'facilities'       => 'nullable|string|max:255',
                'skills'           => 'nullable|string|max:255',
                'initiatives'      => 'nullable|string',

                // E. Prioritization
                'priority'         => 'required|in:High,Medium,Low',
                'solutions'        => 'nullable|string',
                'top_need_1'       => 'required|string|max:255',
                'top_need_2'       => 'nullable|string|max:255',
                'top_need_3'       => 'nullable|string|max:255',
            ]);


            DncRecord::create($validated);

            return redirect()->route('dnc.view')
                ->with('success', 'DNC record added successfully!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'An error occurred while saving the record: ' . $e->getMessage());
        }
    }


    public function show($id)
    {
        $record = DncRecord::findOrFail($id);
        return view('administrator.dnc.dncshow', compact('record'));
    }


    public function destroy($id)
    {
        $record = DncRecord::findOrFail($id);
        $record->delete();

        return redirect()->route('dnc.view')->with('success', 'Record deleted successfully');
    }

    # Edit method
    public function dncedit($id)
    {
        $dnc = DncRecord::findOrFail($id);
        // reuse adddnc.blade.php for editing
        return view('administrator.dnc.adddnc', compact('dnc'));
    }

    #Update method
    public function dncupdate(Request $request, $id)
    {
        try {
            $dnc = DncRecord::findOrFail($id);

            $validated = $request->validate([
                'date'             => 'required|date',
                'assessor'         => 'nullable|string|max:255',
                'event'            => 'required|string|max:255',
                'province'         => 'required|string|max:255',
                'municipality'     => 'required|string|max:255',
                'barangay'         => 'required|string|max:255',
                'households'       => 'nullable|integer',
                'individuals'      => 'nullable|integer',
                'pop_male'         => 'nullable|integer',
                'pop_female'       => 'nullable|integer',
                'pop_children'     => 'nullable|integer',
                'pop_elderly'      => 'nullable|integer',
                'pop_pwds'         => 'nullable|integer',
                'houses_full'      => 'nullable|integer',
                'houses_partial'   => 'nullable|integer',
                'infrastructure'   => 'nullable|array',
                'crop_type'        => 'nullable|string|max:255',
                'crop_hectares'    => 'nullable|numeric',
                'livestock_type'   => 'nullable|string|max:255',
                'livestock_number' => 'nullable|integer',
                'tools_destroyed'  => 'nullable|string|max:255',
                'facilities_affected' => 'nullable|in:Yes,No',
                'facilities_notes'    => 'nullable|string',
                'needs'            => 'nullable|array',
                'needs_other'      => 'nullable|string',
                'groups'           => 'nullable|string|max:255',
                'facilities'       => 'nullable|string|max:255',
                'skills'           => 'nullable|string|max:255',
                'initiatives'      => 'nullable|string',
                'priority'         => 'required|in:High,Medium,Low',
                'solutions'        => 'nullable|string',
                'top_need_1'       => 'required|string|max:255',
                'top_need_2'       => 'nullable|string|max:255',
                'top_need_3'       => 'nullable|string|max:255',
            ]);

            $dnc->update($validated);

            return redirect()->route('dnc.view')->with('success', 'DNC record updated successfully!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'An error occurred while updating the record: ' . $e->getMessage());
        }
    }
}
