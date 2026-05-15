<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CampaignController;
use Illuminate\Support\Facades\Auth;

// Campaign listing page
Route::get('/campaignpage', [CampaignController::class, 'campaignpage'])
    ->middleware('throttle:public')
    ->name('campaignpage');
// Show a single campaign (unique views)
Route::get('/campaignview/{id}', [CampaignController::class, 'campaignview'])
    ->middleware('throttle:public')
    ->name('campaign.view');
// Campaign creation page
Route::get('/campaign/create', [CampaignController::class, 'createpage'])
    ->middleware(['auth', 'throttle:public'])
    ->name('campaign.createpage');
// Store new campaign
Route::post('/campaigns', [CampaignController::class, 'createcampaign'])
    ->middleware(['auth', 'throttle:upload'])
    ->name('campaign.create');

Route::post('/donations/store', [CampaignController::class, 'donate'])
    ->middleware(['throttle:payment', 'throttle:upload'])
    ->name('donations.store');

// Add this route
Route::get('/notifications', [CampaignController::class, 'notificationpage'])
    ->middleware(['auth', 'throttle:api'])
    ->name('notifications.index');



// Mark-all notification
Route::post('/notifications/mark-all-read', function () {
    $updated = Auth::user()
        ->notifications()
        ->whereNull('read_at')
        ->update(['read_at' => now()]);

    return response()->json(['success' => true, 'updated' => $updated]);
})->middleware(['auth', 'throttle:api'])->name('notifications.mark-all-read');

// Mark single notification as read. Keep this after the specific mark-all route.
Route::post('/notifications/{notification}/read', function ($notificationId) {
    $notification = Auth::user()->notifications()->where('id', $notificationId)->first();

    if ($notification) {
        $notification->forceFill(['read_at' => now()])->save();
    }

    return response()->json(['success' => true]);
})->middleware(['auth', 'throttle:api'])->name('notifications.read');



Route::get('/notifications/all', function () {
    $notifications = Auth::user()->notifications()->latest()->get();

    return response()->json($notifications);
})->middleware(['auth', 'throttle:api']);


// Delete routes for notifications
Route::post('/notifications/delete-all', [CampaignController::class, 'deleteAll'])
    ->middleware(['auth', 'throttle:api'])
    ->name('notifications.delete-all');
Route::post('/notifications/delete-selected', [CampaignController::class, 'deleteSelected'])
    ->middleware(['auth', 'throttle:api'])
    ->name('notifications.delete-selected');
Route::delete('/notifications/{id}', [CampaignController::class, 'delete'])
    ->middleware(['auth', 'throttle:api'])
    ->name('notifications.delete');
