<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InkindController;
use App\Models\ImpactReport;
use App\Models\DropOffPoint;
use App\Models\InkindDonation;


Route::get('/inkindpage', [InkindController::class, 'inkindpage'])
    ->middleware('throttle:public')
    ->name('inkind.page');
Route::match(['get', 'post'], '/inkindmodal', [InkindController::class, 'inkindmodal'])
    ->middleware('throttle:public')
    ->name('inkindmodal');

Route::post('/inkind-donate', [InkindController::class, 'inkindsubmit'])
    ->middleware(['throttle:payment', 'throttle:upload'])
    ->name('inkind.donate');

Route::get('/my-donations', [InkindController::class, 'myDonations'])
    ->middleware(['auth', 'throttle:api'])
    ->name('donations.track');
Route::get('/stats', [InkindController::class, 'getStats'])->middleware('throttle:api');

// routes/web.php

Route::get('/in-kind-tracking', function () {
    // Get impact reports with donations for the slider
    $impactReports = ImpactReport::orderBy('report_date', 'desc')
        ->get();



    // Get all active drop-off points
    $dropOffPoints = DropOffPoint::where('is_active', true)
        ->orderBy('name')
        ->get();



    return response()
        ->view('inkind.tracking', [
            'impactReports' => $impactReports,
            'dropOffPoints' => $dropOffPoints,
        ])
        ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
        ->header('Pragma', 'no-cache')
        ->header('Expires', '0');
})->middleware('throttle:public')->name('inkind.tracking');



Route::get('/api/impact-reports/{id}', function ($id) {
    $report = ImpactReport::findOrFail($id);

    return response()->json([
        'title' => $report->title,
        'description' => $report->description,
        'report_date_formatted' => \Carbon\Carbon::parse($report->report_date)->format('F d, Y'),
        'donations' => $report->donations->map(function ($donation) {
            return [
                'item_name' => $donation->item_name,
                'quantity' => $donation->quantity,
                'category' => $donation->category,
            ];
        }),
        // Resolve R2 keys to full public URLs so the frontend can render them directly.
        'photos' => collect($report->photos ?? [])->map(fn ($key) => file_url($key))->filter()->values(),
    ]);
})->middleware('throttle:api');
