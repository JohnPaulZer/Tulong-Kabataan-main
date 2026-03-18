<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DropOffPoint;
use App\Models\InKindDonation;
use App\Notifications\InKindDonationConfirmationNotification;
use Illuminate\Support\Facades\Auth;
use App\Models\ImpactReport;

class InkindController
{

    protected function noCacheView($view, $data = [])
    {
        $response = response()->view($view, $data);

        return $response->header('Cache-Control', 'no-cache, no-store, must-revalidate')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }

    public function inkindpage()
    {
        return $this->noCacheView('inkind.inkindpage');
    }

    public function inkindmodal()
    {
        $locations = DropOffPoint::all();
        return view('inkind.inkindmodal', compact('locations'));
    }

    public function inkindsubmit(Request $request)
    {
        // Validate input
        $request->validate([
            'donor_name'   => 'nullable|string|max:255',
            'donor_email'  => 'required|email|max:255',
            'donor_phone'  => 'nullable|string|max:20',
            'dropoff_id'   => 'required|exists:drop_off_points,dropoff_id',
            'items'        => 'required|array|min:1',
            'items.*.item_name' => 'required|string|max:255',
            'items.*.category' => 'required|string|max:255',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.description' => 'nullable|string|max:1000',
        ]);

        $dropoffPoint = DropOffPoint::findOrFail($request->dropoff_id);
        $donations = [];

        // Create donation records for each item
        foreach ($request->items as $item) {
            $donation = InKindDonation::create([
                'user_id'     => Auth::check() ? Auth::user()->user_id : null,
                'donor_name'  => $request->donor_name ?? (Auth::check() ? Auth::user()->first_name . ' ' . Auth::user()->last_name : null),
                'donor_email' => $request->donor_email ?? (Auth::check() ? Auth::user()->email : null),
                'donor_phone' => $request->donor_phone ?? (Auth::check() ? Auth::user()->phone_number : null),
                'dropoff_id'  => $request->dropoff_id,
                'item_name'   => $item['item_name'],
                'category'    => $item['category'],
                'description' => $item['description'] ?? null,
                'quantity'    => $item['quantity'],
                'status'      => 'Scheduled',
            ]);

            $donations[] = $donation;
        }

        // Send notification to donor if they are registered user
        if (Auth::check()) {
            $user = Auth::user();

            // Create a summary message for multiple items
            $totalItems = count($donations);
            $itemSummary = collect($donations)->map(function ($donation) {
                return "{$donation->quantity} {$donation->item_name}";
            })->implode(', ');

            // Send notification
            $user->notify(new InKindDonationConfirmationNotification($donations, $dropoffPoint, $totalItems, $itemSummary));
        }

        return redirect()->route('inkind.page')->with('toast_message', 'Thank you for your donations! Our staff will be ready to receive your items.');
    }


    public function getStats()
    {
        // Unique registered donors by user_id
        $registeredDonors = InKindDonation::whereNotNull('user_id')
            ->distinct('user_id')
            ->count('user_id');

        // Unique guest donors by donor_email
        $guestDonors = InKindDonation::whereNull('user_id')
            ->whereNotNull('donor_email')
            ->distinct('donor_email')
            ->count('donor_email');

        // Total donors = registered + guest
        $donors = $registeredDonors + $guestDonors;

        // Donations made = received
        $donationsMade = InKindDonation::where('status', 'Received')->count();

        // Upcoming donations = scheduled
        $upcomingDonations = InKindDonation::where('status', 'Scheduled')->count();



        return response()->json([
            'donors' => $donors,
            'donationsMade' => $donationsMade,
            'upcomingDonations' => $upcomingDonations,
        ]);
    }
}
