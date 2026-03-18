<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdministratorController;
use App\Http\Controllers\AttendanceEmailController;



Route::prefix('administrator')->group(function () {

    Route::get('/', [AdministratorController::class, 'showLoginForm'])
        ->name('admin.login')
        ->middleware('guest');
    Route::post('/login', [AdministratorController::class, 'login'])
        ->name('login.submit');
    Route::get('/dashboard', [AdministratorController::class, 'dashboard'])
        ->name('admin.home');
    Route::get('/logout', [AdministratorController::class, 'logout'])
        ->name('admin.logout');

    //===========================================ACCOUNT VERIFCATION===============================================
    Route::get('/accounts', [AdministratorController::class, 'accountpage'])->name('account.page');
    Route::post('/decision', [AdministratorController::class, 'decision'])->name('decision');
    Route::get('/accounts/stats', [AdministratorController::class, 'getAccountStats'])
        ->name('accounts.stats');
    //===================================================================================================================

    //===========================================CAMPAIGN ROUTE==========================================================
    Route::get('/campaign', [AdministratorController::class, 'campaignview'])->name('campaign.page');

    Route::post('/admin/manual-requests/{id}/approve', [AdministratorController::class, 'approveManual'])
        ->name('admin.manual.requests.approve');
    Route::post('/admin/manual-requests/{id}/reject', [AdministratorController::class, 'rejectManual'])
        ->name('admin.manual.requests.reject');
    Route::get('/admin/campaigns/latest', [AdministratorController::class, 'getLatestCampaigns'])
        ->name('admin.campaigns.latest');
    Route::get('/admin/campaigns/{id}', [AdministratorController::class, 'showcampaigndetails'])->name('admin.campaign.show');

    Route::get('/administrator/dashboard/stats', [AdministratorController::class, 'getCampaignStats'])
        ->name('admin.dashboard.stats');

    Route::get('/admin/monthly-funds', [AdministratorController::class, 'getMonthlyFunds'])
        ->name('admin.monthly.funds');

    Route::get('/admin/campaigns/{campaignId}/export-pdf', [AdministratorController::class, 'exportCampaignPdf'])
        ->name('admin.campaigns.export.pdf');
    //===================================================================================================================



    //===========================================iN KIND ROUTE==========================================================
    Route::get('/in-kind', [AdministratorController::class, 'admininkindpage'])->name('admininkind.page');
    Route::post('/add-location', [AdministratorController::class, 'addlocation'])->name('location.add');
    Route::put('/update-location/{dropoff_id}', [AdministratorController::class, 'updatelocation'])
        ->name('location.update');
    Route::delete('/delete-location/{dropoff_id}', [AdministratorController::class, 'deletelocation'])->name('location.delete');
    Route::put('/toggle-location/{dropoff_id}', [AdministratorController::class, 'togglelocation'])
        ->name('location.toggle');
    Route::post('/in-kind/update-status', [AdministratorController::class, 'updatestatus'])->name('donations.updateStatus');
    Route::put('/kind-donations/{id}/update', [AdministratorController::class, 'updatemodal'])
        ->name('in-donations.update');
    Route::get('/donations/latest', [AdministratorController::class, 'getLatestDonations'])
        ->name('donations.latest');
    Route::get('/donations/chart-data', [AdministratorController::class, 'getInKindChartData'])
        ->name('donations.chart-data');
    Route::get('/donations/category-chart-data', [AdministratorController::class, 'getCategoryChartData'])
        ->name('donations.category-chart-data');

    Route::post('/donations/impact-reports/create', [AdministratorController::class, 'impactreportstore'])
        ->name('impact-reports.store');

    Route::get('/administrator/donations/received', [AdministratorController::class, 'getReceivedDonations'])
        ->name('donations.getReceived');
    //==================================================================================================================

    //===================================================Event ROUTE  =========================================================
    Route::get('/create-event', [AdministratorController::class, 'createevent'])->name('createevent');
    Route::post('/submit-event', [AdministratorController::class, 'submitevent'])->name('submitevent');

    Route::get('/event', [AdministratorController::class, 'eventview'])->name('adminevent.page');
    Route::put('/volunteer/{registration_id}/update', [AdministratorController::class, 'updateVolunteerStatus']);

    Route::get('/events/statistics', [AdministratorController::class, 'getEventStatistics'])
        ->name('events.statistics');
    Route::get('/events/live', [AdministratorController::class, 'live'])->name('events.live');
    Route::get('/volunteers/live', [AdministratorController::class, 'volunteerLive'])
        ->name('volunteers.live');
    Route::get('/volunteer-participation-data', [AdministratorController::class, 'getVolunteerParticipationData'])
        ->name('admin.volunteer.data');
    Route::get('/volunteers/profile/{email}', [AdministratorController::class, 'volunteerProfile']);
    Route::get('/admin/volunteer/data', [AdministratorController::class, 'volunteerData'])->name('admin.volunteer.data');



    //==================================================================================================================

    //====================================== DNC ROUTE  =========================================================

    Route::get('/dncrecords', [AdministratorController::class, 'dncview'])->name('dnc.view');
    Route::get('/add-dncrecords', [AdministratorController::class, 'dncadd'])->name('dnc.add');
    Route::post('/submit-dncrecords', [AdministratorController::class, 'dncstore'])->name('dnc.submit');
    Route::get('/dnc/{id}', [AdministratorController::class, 'show'])->name('dnc.show');

    Route::delete('/dnc/{id}', [AdministratorController::class, 'destroy'])->name('dnc.destroy');
    Route::get('/dnc/{id}/edit', [AdministratorController::class, 'dncedit'])->name('dnc.edit');
    Route::put('/dnc/{id}', [AdministratorController::class, 'dncupdate'])->name('dnc.update');
});


//IN-KIND DONATIONS PAGE PAGINATION
Route::post('/administrator/donations/paginate', [AdministratorController::class, 'paginateDonations'])->name('donations.paginate');
