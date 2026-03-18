<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Auth;

Route::get('/profile', [ProfileController::class, 'profileview'])->name('profile');

//VERIFCATION ACCOUNT ROUTE
Route::get('/verifypage', [ProfileController::class, 'verifypage'])->name('verify.page'); // GET page
Route::post('/submit', [ProfileController::class, 'submitverifcation'])->name('submit.verification');   // POST submit

Route::get('/check-verification-status', function () {
    if (!Auth::check()) {
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    $user = Auth::user();
    $status = strtolower(optional($user->identityStatus)->status ?? '');

    return response()->json([
        'status' => $status,
        'updated_at' => optional($user->identityStatus)->updated_at
    ]);
});

Route::put('/profile/change-password', [ProfileController::class, 'changePassword'])->name('profile.change-password');

Route::post('/profile/change-photo', [ProfileController::class, 'changePhoto'])
    ->name('profile.change-photo');

Route::put('/profile/update', [ProfileController::class, 'update'])->name('profile.update');


//===============================================PROFILE DASHBOARD CAMPAIGN PART========================================
Route::get('/profile/dashboard', [ProfileController::class, 'profiledash'])->name('profile.dash');
//FOR AJAX LIVE
Route::get('/profile/dash/data', [ProfileController::class, 'profileDashData'])->name('profile.dash.data');
//DELETE CAMPAIGN
Route::delete('/campaigns/{campaign}', [ProfileController::class, 'destroy'])
    ->name('campaigns.destroy');
//ENDED CAMPAIGN
Route::patch('/campaigns/{id}/end', [ProfileController::class, 'end'])->name('campaigns.end');

//ANALYTICS AND STATISTIC CHART CAMPAIGN
Route::get('/campaigns/analytics', [ProfileController::class, 'analytics'])
    ->name('campaigns.analytics');
Route::get('/campaigns/donations-over-time', [ProfileController::class, 'donationsOverTime'])
    ->name('campaigns.donationsOverTime');
Route::get('/campaigns/top', [ProfileController::class, 'topCampaigns'])
    ->name('campaigns.top');
Route::get('/donations/my-analytics', [ProfileController::class, 'myDonationsAnalytics'])
    ->name('donations.myAnalytics');

// VIEW ALL DONATION MODAL
Route::get('/donations/all', [ProfileController::class, 'allMyDonations'])
    ->name('donations.all');
//MINI FEED RECENT DONATION
Route::get('/campaigns/recent-donations', [ProfileController::class, 'recentDonations'])
    ->name('campaigns.recentDonations');

Route::get('/donations/allRecent', [ProfileController::class, 'allRecent'])
    ->name('donations.allRecent');

//DONATION MANUAL REQEUSEST
Route::post('/donations/{donation}/report-fake', [ProfileController::class, 'reportFake'])
    ->name('donations.reportFake');

Route::post('/campaigns/{campaign}/manual-request', [ProfileController::class, 'manualadd'])
    ->name('manual.requests.store');

//ADDED ROUTE
Route::get('/campaigns/{campaign}/export-donations', [ProfileController::class, 'exportDonations'])->name('campaigns.export.donations');

//EXPORT PDF ROUTE
Route::get('/campaigns/{campaign}/export-donations-pdf', [ProfileController::class, 'exportDonationsPDF'])->name('campaigns.export-pdf');

//ADDED ACTION UPDATE
Route::post('/campaign-updates', [ProfileController::class, 'storeUpdate'])->name('campaign.updates.store');
//=========================================================================================================================



//===============================================PROFILE DASHBOARD EVENT PART========================================

Route::get('/profile/event', [ProfileController::class, 'profileevent'])->name('profile.event');

Route::get('/profile/events/refresh', [ProfileController::class, 'refreshProfileEvents'])
    ->name('profile.refreshEvents');

Route::delete('/events/{event}/unregister', [ProfileController::class, 'eventUnregister'])
    ->name('events.unregister');

Route::get('/profile/refresh-events', [ProfileController::class, 'refreshEvents'])

    ->name('profile.refreshEvents');
Route::get('/profile/refresh-stats', [ProfileController::class, 'refreshStats'])->name('profile.refreshStats');
//====================================================================================================================


//===============================================PROFILE DASHBOARD In-Kind PART========================================
Route::get('/profile/inkind', [ProfileController::class, 'profileinkind'])->name('profile.inkind');
Route::patch('/inkind/{donation}/cancel', [ProfileController::class, 'cancelInKind'])
    ->name('inkind.cancel');
Route::delete('/inkind/{donation}', [ProfileController::class, 'deleteInKind'])
    ->name('inkind.delete');
// routes/web.php
Route::get('/inkind/list', [ProfileController::class, 'list'])
    ->name('inkind.list');
//====================================================================================================================


Route::get('/about-us', [ProfileController::class, 'aboutus'])->name('about.us');