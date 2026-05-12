<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CampaignController;
use Illuminate\Support\Facades\Auth;

// Campaign listing page
Route::get('/campaignpage', [CampaignController::class, 'campaignpage'])->name('campaignpage');
// Show a single campaign (unique views)
Route::get('/campaignview/{id}', [CampaignController::class, 'campaignview'])->name('campaign.view');
// Campaign creation page
Route::get('/campaign/create', [CampaignController::class, 'createpage'])
    ->middleware('auth')
    ->name('campaign.createpage');
// Store new campaign
Route::post('/campaigns', [CampaignController::class, 'createcampaign'])
    ->middleware('auth')
    ->name('campaign.create');

Route::post('/donations/store', [CampaignController::class, 'donate'])->name('donations.store');

// Add this route
Route::get('/notifications', [CampaignController::class, 'notificationpage'])
    ->middleware('auth')
    ->name('notifications.index');



// Mark single notification as read
Route::post('/notifications/{notification}/read', function ($notificationId) {
    $notification = Auth::user()->notifications()->where('id', $notificationId)->first();

    if ($notification) {
        $notification->forceFill(['read_at' => now()])->save();
    }

    return response()->json(['success' => true]);
})->middleware('auth')->name('notifications.read');

// Mark-all notification
Route::post('/notifications/mark-all-read', function () {
    Auth::user()->unreadNotifications()->update(['read_at' => now()]);
    return response()->json(['success' => true]);
})->middleware('auth')->name('notifications.mark-all-read');



Route::get('/notifications/all', function () {
    $notifications = Auth::user()->notifications()->latest()->get();

    return response()->json($notifications);
})->middleware('auth');


// Delete routes for notifications
Route::post('/notifications/delete-all', [CampaignController::class, 'deleteAll'])
    ->middleware('auth')
    ->name('notifications.delete-all');
Route::post('/notifications/delete-selected', [CampaignController::class, 'deleteSelected'])
    ->middleware('auth')
    ->name('notifications.delete-selected');
Route::delete('/notifications/{id}', [CampaignController::class, 'delete'])
    ->middleware('auth')
    ->name('notifications.delete');
