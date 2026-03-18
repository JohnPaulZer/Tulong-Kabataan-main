<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EventController;
use Illuminate\Http\Request;
use App\Models\Event;
use Illuminate\Support\Facades\Auth;
use App\Models\EventRegistration;

Route::get('/eventpage', [EventController::class, 'eventpage'])->name('event.page');

Route::get('/event-register/{event}', [EventController::class, 'registerpage'])->name('event.register');

Route::post('/submit-registration', [EventController::class, 'registerevent'])->name('submitregistration');

Route::get('/eventview/{event_id}', [EventController::class, 'eventview'])->name('event.view');

Route::get('/event-modal/{event_id}', [EventController::class, 'eventModal'])->name('event.modal');


Route::get('/events/updates', function (Request $request) {
    try {
        $since = $request->query('since', now()->subMinutes(5)->toISOString());
        $userId = Auth::id();

        // Get events that have been updated since the given time
        $events = Event::with('volunteerRoles')
            ->where(function ($query) use ($since) {
                $query->where('updated_at', '>', $since)
                    ->orWhere('created_at', '>', $since);
            })
            ->get()
            ->map(function ($event) use ($userId) {
                // Check if user is registered for this event
                $isRegistered = false;
                if ($userId) {
                    $isRegistered = EventRegistration::where('user_id', $userId)
                        ->where('event_id', $event->event_id)
                        ->exists();
                }

                return [
                    'event_id' => $event->event_id,
                    'title' => $event->title,
                    'description' => $event->description,
                    'photo' => $event->photo,
                    'start_date' => $event->start_date,
                    'end_date' => $event->end_date,
                    'location' => $event->location,
                    'deadline' => $event->deadline,
                    'updated_at' => $event->updated_at,
                    'created_at' => $event->created_at,
                    'is_registered' => $isRegistered, // Add this
                ];
            });

        return response()->json([
            'success' => true,
            'events' => $events,
            'lastUpdate' => now()->toISOString(),
            'count' => $events->count()
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage()
        ], 500);
    }
});
