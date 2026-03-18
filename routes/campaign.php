<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CampaignController;
use Illuminate\Support\Facades\Auth;

// Campaign listing page
Route::get('/campaignpage', [CampaignController::class, 'campaignpage'])->name('campaignpage');
// Show a single campaign (unique views)
Route::get('/campaignview/{id}', [CampaignController::class, 'campaignview'])->name('campaign.view');
// Campaign creation page
Route::get('/campaign/create', [CampaignController::class, 'createpage'])->name('campaign.createpage');
// Store new campaign
Route::post('/campaigns', [CampaignController::class, 'createcampaign'])->name('campaign.create');

Route::post('/donations/store', [CampaignController::class, 'donate'])->name('donations.store');

// Add this route
Route::get('/notifications', [CampaignController::class, 'notificationpage'])->name('notifications.index');



// Mark single notification as read
Route::post('/notifications/{notification}/read', function ($notificationId) {
    $notification = Auth::user()->notifications()->where('id', $notificationId)->first();

    if ($notification) {
        $notification->markAsRead();
    }

    return response()->json(['success' => true]);
})->name('notifications.read');

// Mark-all notification
Route::post('/notifications/mark-all-read', function () {
    Auth::user()->unreadNotifications->markAsRead();
    return response()->json(['success' => true]);
})->name('notifications.mark-all-read');



Route::get('/notifications/all', function () {
    $notifications = Auth::user()->notifications()->latest()->get();

    return response()->json($notifications);
});


// Delete routes for notifications
Route::post('/notifications/delete-all', [CampaignController::class, 'deleteAll'])->name('notifications.delete-all');
Route::post('/notifications/delete-selected', [CampaignController::class, 'deleteSelected'])->name('notifications.delete-selected');
Route::delete('/notifications/{id}', [CampaignController::class, 'delete'])->name('notifications.delete');