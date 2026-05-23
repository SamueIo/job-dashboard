<?php

namespace App\Http\Controllers;

use App\Models\Event;

use Illuminate\Support\Facades\Auth;

use Inertia\Inertia;
use App\Services\GoogleCalendarService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CalendarController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $events = Event::query()

            ->where('user_id', $user->id)
            ->whereNull('hidden_at')
            ->orderBy('start_at')

            ->get();

        $nextInterview = Event::query()

            ->where('user_id', $user->id)

            ->where('start_at', '>=', now())

            ->where('category', 'interview')

            ->orderBy('start_at')

            ->first();

        $upcomingInterviews = Event::query()

            ->where('user_id', $user->id)

            ->where('start_at', '>=', now())

            ->where('category', 'interview')

            ->orderBy('start_at')

            ->take(5)

            ->get();

        $stats = [

            'total' => $events->where('category', 'interview')->count(),

            'upcoming' => $events->where('category', 'interview')

                ->filter(fn ($event) =>
                    Carbon::parse(
                        $event->start_at
                    )->isFuture()
                )

                ->count(),

            'thisWeek' => $events->where('category', 'interview')

                ->filter(fn ($event) =>
                    Carbon::parse(
                        $event->start_at
                    )->isCurrentWeek()
                )

                ->count(),

            'nextInterview' => $nextInterview

                ? [

                    'company' =>
                        $nextInterview->company,

                    'date' =>
                        Carbon::parse(
                            $nextInterview->start_at
                        )->format('M d, H:i'),
                ]

                : null,
        ];
        $readyToSync = Event::query()

            ->where('user_id', $user->id)

            ->whereNull('google_event_id')

            ->whereNotNull('start_at')

            ->latest()

            ->take(5)

            ->get();

        return Inertia::render('Calendar', [

            'events' => $events,

            'stats' => $stats,

            'upcomingInterviews' =>
                $upcomingInterviews,

            'googleCalendarSyncEnabled' =>
                (bool) $user
                    ->google_calendar_sync_enabled,
            
            'readyToSync' => $readyToSync,
        ]);
    }

    public function toggleGoogleSync()
    {
        $user = Auth::user();

        $user->update([

            'google_calendar_sync_enabled' =>

                ! $user->google_calendar_sync_enabled,
        ]);

        return back();
    }

    public function sync(GoogleCalendarService $googleCalendarService)
    {
        $googleCalendarService->sync(
            Auth::user()
        );
        return back();
    }

    public function syncEvent(Event $event,GoogleCalendarService $googleCalendarService)  {
        $googleCalendarService->syncEvent($event);

        return back();
    }

    public function syncAll(GoogleCalendarService $googleCalendarService) 
    {
        $events = Event::query()

            ->where('user_id', auth()->id())

            ->whereNull('google_event_id')

            ->whereNotNull('start_at')

            ->get();

        foreach ($events as $event) {

            $googleCalendarService
                ->syncEvent($event);
        }

        return back();
    }

    public function store(Request $request)
    {
        $event = Event::create([

            'user_id' => auth()->id(),

            'title' => $request->title,

            'company' => $request->company,

            'description' => $request->description,

            'start_at' => $request->start_at,

            'end_at' => $request->end_at,

            'location' => $request->location,

            'meeting_link' => $request->meeting_link,

            'type' => $request->type,

            'source' => 'local',

            'category' => $request->type ?? 'personal',
        ]);

        if ($request->sync_to_google) {

            app(GoogleCalendarService::class)
                ->syncEvent($event);
        }

        return back();
    }

    public function update(Request $request,Event $event,GoogleCalendarService $googleCalendarService)
    {

        abort_if(
            $event->user_id !== auth()->id(),
            403
        );

        $validated = $request->validate([

            'title' => ['required', 'string'],

            'description' => ['nullable', 'string'],

            'start_at' => ['required', 'date'],

            'end_at' => ['nullable', 'date'],

            'type' => ['nullable', 'string'],
        ]);

        $event->update($validated);

        /*
        |----------------------------------------------------------------------
        | UPDATE GOOGLE EVENT
        |----------------------------------------------------------------------
        */

        if (
            $request->sync_to_google &&
            $event->google_event_id
        ) {
            $googleCalendarService->updateEvent($event);
        }

        return back();
    }

    public function hide( Event $event)
    {
        abort_unless($event->user_id === auth()->id(),403);

        $event->update(['hidden_at' => now()]);

        return back();
    }


    public function forceDelete(Event $event,GoogleCalendarService $googleCalendarService) 
    {
        abort_unless($event->user_id === auth()->id(), 403);

        if ($event->source === 'google' && $event->google_event_id) {
            $googleCalendarService->deleteEvent(
                auth()->user(),
                $event->google_event_id
            );
        }

        $event->delete();

        return back();
    }
}