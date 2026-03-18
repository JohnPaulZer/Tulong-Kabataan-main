<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\VolunteerRole;
use App\Models\EventRegistration;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Mail\EventReminderMail;
use Illuminate\Support\Facades\Mail;
use App\Jobs\SendEventReminderJob;
use App\Notifications\EventRegistrationNotification;
use App\Notifications\EventReminderNotification;
use Illuminate\Support\Str;

class EventController
{

    protected function noCacheView($view, $data = [])
    {
        $response = response()->view($view, $data);

        return $response->header('Cache-Control', 'no-cache, no-store, must-revalidate')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }

    public function eventpage()
    {
        $user = Auth::user();

        // Fetch all events - remove status filter
        $events = Event::all();

        // Prepare calendar events (for all events)
        $calendarEvents = $events->map(function ($event) {
            return [
                'id' => $event->event_id,
                'title' => $event->title,
                'description' => $event->description,
                'photo' => $event->photo,
                'start_date' => $event->start_date,
                'end_date' => $event->end_date,
            ];
        });

        // ✅ If user is not logged in, set empty collections
        $registeredEvents = collect();
        $registeredEventIds = [];
        $registeredCalendarEvents = collect();

        if ($user) {
            // Fetch the current user's registered events
            $registeredEvents = EventRegistration::with('event')
                ->where('user_id', $user->user_id)
                ->get();

            // Create a quick lookup array of registered event IDs
            $registeredEventIds = $registeredEvents->pluck('event_id')->toArray();

            // Prepare calendar events only for registered events
            $registeredCalendarEvents = $registeredEvents->map(function ($reg) {
                return [
                    'id' => $reg->event->event_id,
                    'title' => $reg->event->title,
                    'start_date' => $reg->event->start_date,
                    'end_date' => $reg->event->end_date,
                ];
            });
        }

        // --- NEW: compute a status for each event ---
        // statuses: 'upcoming', 'ongoing', 'ended'
        $now = Carbon::now(); // uses app timezone
        $eventStatuses = [];
        foreach ($events as $event) {
            $start = Carbon::parse($event->start_date);
            $end = Carbon::parse($event->end_date);
            $deadline = $event->deadline ? Carbon::parse($event->deadline) : null;

            if ($now->between($start, $end)) {
                $status = 'ongoing';
            } elseif ($now->greaterThan($end)) {
                $status = 'ended';
            } elseif ($deadline && $now->greaterThan($deadline)) {
                $status = 'closed'; // registration closed before event
            } else {
                $status = 'upcoming';
            }

            $eventStatuses[$event->event_id] = $status;
        }

        // Pass everything to the view
        return $this->noCacheView('event.eventpage', compact(
            'events',
            'calendarEvents',
            'registeredEvents',
            'registeredEventIds',
            'registeredCalendarEvents',
            'eventStatuses'
        ));
    }

    public function registerpage(Event $event)
    {
        $roles = $event->volunteerRoles()->get();
        return view('event.eventregister', compact('event', 'roles'));
    }

    // public function eventview($event_id)
    // {
    //     $user = Auth::user();
    //     $event = Event::findOrFail($event_id);

    //     // Check if logged in user is registered
    //     $isRegistered = false;
    //     if ($user) {
    //         $isRegistered = EventRegistration::where('user_id', $user->user_id)
    //             ->where('event_id', $event->event_id)
    //             ->exists();
    //     }

    //     // Compute event status
    //     $now = Carbon::now();
    //     $start = Carbon::parse($event->start_date);
    //     $end = Carbon::parse($event->end_date);
    //     $deadline = $event->deadline ? Carbon::parse($event->deadline) : null;

    //     if ($deadline && $now->greaterThan($deadline) && $now->lessThan($start)) {
    //         $status = 'closed';
    //     } elseif ($now->between($start, $end)) {
    //         $status = 'ongoing';
    //     } elseif ($now->greaterThan($end)) {
    //         $status = 'ended';
    //     } else {
    //         $status = 'upcoming';
    //     }

    //     // ✅ Participants info
    //     $participantCount = EventRegistration::where('event_id', $event->event_id)
    //         ->where('status', 'registered')
    //         ->count();

    //     $participants = EventRegistration::where('event_id', $event->event_id)
    //         ->where('status', 'registered')
    //         ->join('user_account', 'event_registrations.user_id', '=', 'user_account.user_id')
    //         ->select('user_account.first_name', 'user_account.last_name', 'user_account.profile_photo_url')
    //         ->take(5)
    //         ->get();

    //     return view('event.eventview', compact('event', 'isRegistered', 'status', 'participantCount', 'participants'));
    // }

    public function eventModal($event_id)
    {
        // Copy all the logic from your eventview method
        $user = Auth::user();
        $event = Event::with('volunteerRoles')->findOrFail($event_id);

        $isRegistered = false;
        if ($user) {
            $isRegistered = EventRegistration::where('user_id', $user->user_id)
                ->where('event_id', $event->event_id)
                ->exists();
        }

        $now = Carbon::now();
        $start = Carbon::parse($event->start_date);
        $end = Carbon::parse($event->end_date);
        $deadline = $event->deadline ? Carbon::parse($event->deadline) : null;

        if ($deadline && $now->greaterThan($deadline) && $now->lessThan($start)) {
            $status = 'closed';
        } elseif ($now->between($start, $end)) {
            $status = 'ongoing';
        } elseif ($now->greaterThan($end)) {
            $status = 'ended';
        } else {
            $status = 'upcoming';
        }

        $participantCount = EventRegistration::where('event_id', $event->event_id)
            ->where('status', 'registered')
            ->count();

        return view('event.event-details-modal', compact('event', 'isRegistered', 'status', 'participantCount'));
    }

    public function registerevent(Request $request)
    {
        $user = Auth::user();
        $userId = $user->user_id ?? null;



        // Basic validation
        $validated = $request->validate([
            'event_id'       => 'required|integer|exists:events,event_id',
            'first_name'     => ['required', 'string', 'max:100'],
            'last_name'      => ['required', 'string', 'max:100'],
            'email'          => ['required', 'email', 'max:150'],
            'phone'          => ['required', 'string', 'max:50'],
            'messenger_link' => ['nullable', 'url', 'max:250'],
            'age'            => ['nullable', 'integer', 'min:10'],
            'sex'            => ['nullable', 'in:male,female'],
            'address'        => ['nullable', 'string', 'max:255'],
            'vroles_id'      => ['nullable', 'integer'],
            'remind_me'        => 'nullable|in:0,1',
            'reminder_minutes' => 'nullable|integer|min:1',
        ]);

        $event = Event::find($validated['event_id']);
        if (! $event) {
            return back()->withInput()->withErrors(['event_id' => 'Selected event not found.']);
        }

        $selectedRole = null;
        if (!empty($validated['vroles_id'])) {
            $selectedRole = VolunteerRole::where('vroles_id', $validated['vroles_id'])
                ->where('event_id', $event->event_id)
                ->first();

            if (! $selectedRole) {
                return back()->withInput()->withErrors(['vroles_id' => 'Selected role is invalid for this event.']);
            }
        }


        if ($userId) {
            $existing = EventRegistration::where('event_id', $event->event_id)
                ->where('user_id', $userId)
                ->first();
        } else {
            $existing = EventRegistration::where('event_id', $event->event_id)
                ->whereRaw('LOWER(email) = ?', [strtolower($validated['email'])])
                ->first();
        }

        if ($existing) {
            return back()->with('warning', 'You have already registered for this event.');
        }

        if ($userId) {
            $userRegistrations = EventRegistration::where('user_id', $userId)
                ->with('event')
                ->get();

            foreach ($userRegistrations as $reg) {
                $existingEvent = $reg->event;
                if (!$existingEvent) {
                    continue;
                }

                $existingStart = Carbon::parse($existingEvent->start_date);
                $existingEnd   = Carbon::parse($existingEvent->end_date);
                $newStart      = Carbon::parse($event->start_date);
                $newEnd        = Carbon::parse($event->end_date);

                // Overlap if existing starts before new ends AND existing ends after new starts
                if ($existingStart->lt($newEnd) && $existingEnd->gt($newStart)) {
                    return back()->with('warning', "Schedule conflict with another event: {$existingEvent->title}.");
                }
            }
        }


        DB::beginTransaction();
        try {
            $registration = EventRegistration::create([
                'user_id'        => $userId,
                'event_id'       => $event->event_id,
                'vroles_id'      => $selectedRole ? $selectedRole->vroles_id : null,
                'first_name'     => $validated['first_name'],
                'last_name'      => $validated['last_name'],
                'email'          => $validated['email'],
                'phone'          => $validated['phone'],
                'messenger_link' => $validated['messenger_link'] ?? null,
                'age'            => $validated['age'] ?? null,
                'sex'            => $validated['sex'] ?? null,
                'address'        => $validated['address'] ?? null,
                'registered_role' => $selectedRole ? $selectedRole->name : null,
                'status'         => 'registered',
                'remind_me'       => $validated['remind_me'] ?? false,
                'reminder_minutes' => $validated['reminder_minutes'] ?? null,
            ]);

            // 🔹 Send notification to user if they are registered
            if ($userId) {
                $user->notify(new EventRegistrationNotification($event, $registration));
            }

            if ($registration->remind_me && $registration->reminder_minutes) {
                $reminderTime = Carbon::parse($event->start_date)
                    ->subMinutes($registration->reminder_minutes);

                $delaySeconds = now()->diffInSeconds($reminderTime, false);

                if ($delaySeconds > 0) {
                    SendEventReminderJob::dispatch($registration)->delay($delaySeconds);
                }
            }

            if ($userId) {
                $this->schedulePushNotificationReminders($user, $event, $registration);
            }


            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withInput()->withErrors(['server' => 'Unable to complete registration. Please try again.']);
        }

        return redirect()->route('event.page', ['event' => $event->event_id])
            ->with('success', 'Registration completed successfully!');
    }

    private function schedulePushNotificationReminders($user, $event, $registration)
    {
        // 24-hour reminder - SCHEDULE as job
        $twentyFourHourReminder = Carbon::parse($event->start_date)->subDay();
        if (now()->diffInSeconds($twentyFourHourReminder, false) > 0) {
            \App\Jobs\SendEventReminderNotificationJob::dispatch($user, $event, $registration, 1440)
                ->delay($twentyFourHourReminder);
        }

        // 1-hour reminder - SCHEDULE as job
        $oneHourReminder = Carbon::parse($event->start_date)->subHour();
        if (now()->diffInSeconds($oneHourReminder, false) > 0) {
            \App\Jobs\SendEventReminderNotificationJob::dispatch($user, $event, $registration, 60)
                ->delay($oneHourReminder);
        }

        // 15-minute reminder - SCHEDULE as job
        $fifteenMinuteReminder = Carbon::parse($event->start_date)->subMinutes(15);
        if (now()->diffInSeconds($fifteenMinuteReminder, false) > 0) {
            \App\Jobs\SendEventReminderNotificationJob::dispatch($user, $event, $registration, 15)
                ->delay($fifteenMinuteReminder);
        }
    }
}
