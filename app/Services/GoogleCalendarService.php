<?php

namespace App\Services;

use App\Models\Event;

use Google\Client;
use Google\Service\Calendar;
use Carbon\Carbon;
use Google\Service\Calendar\Event as GoogleEvent;

class GoogleCalendarService
{
    public function sync($user): void
    {

        if (
            ! $user->google_calendar_sync_enabled ||
            ! $user->google_token
        ) {
            return;
        }

        try {

            $client = $this->getClient($user);

            $service = new Calendar($client);

            $googleEvents = $service
                ->events
                ->listEvents('primary', [

                    'timeMin' => now()
                        ->subMonth()
                        ->toRfc3339String(),

                    'singleEvents' => true,

                    'orderBy' => 'startTime',
                ]);

            foreach (
                $googleEvents->getItems()
                as $googleEvent
            ) {
       

                Event::updateOrCreate(

                    [
                        'google_event_id' => $googleEvent->id,
                    ],

                    [
                        'user_id' => $user->id,

                        'title' =>
                            $googleEvent->getSummary()
                            ?: 'Google Event',

                        'company' =>
                            $googleEvent->getSummary()
                            ?: 'Google Event',

                        'description' =>
                            $googleEvent->getDescription(),

                        'start_at' => Carbon::parse(
                            $googleEvent->start->dateTime
                            ?? $googleEvent->start->date
                        ),

                        'end_at' => isset($googleEvent->end)
                            ? Carbon::parse(
                                $googleEvent->end->dateTime
                                ?? $googleEvent->end->date
                            )
                            : null,

                        'location' =>
                            $googleEvent->getLocation(),

                        'type' => null,

                        'source' => 'google',
                    ]
                );
            }

        } catch (\Exception $e) {
            logger()->error(
                'Google Calendar Sync Error: '
                . $e->getMessage()
            );
        }
    }
    
    protected function detectCategory(string $text): string 
    {
    
        $text = str($text)->lower();
    
        $keywords = [
    
            // EN
            'interview',
            'recruiter',
            'technical',
            'screening',
            'onsite',
    
            // SK/CZ
            'pohovor',
            'pracovný pohovor',
            'pracovni pohovor',
    
            // DE
            'bewerbungsgespräch',
    
            // FR
            'entretien',
    
            // ES
            'entrevista',
    
            // IT
            'colloquio',
        ];
    
        foreach ($keywords as $keyword) {
    
            if ($text->contains($keyword)) {
    
                return 'interview';
            }
        }
    
        return 'other';
    }

    protected function getClient($user)
    {
        $client = new Client();

        $client->setClientId(
            config('services.google.client_id')
        );

        $client->setClientSecret(
            config('services.google.client_secret')
        );

        $client->setAccessToken(
            $user->google_token
        );

        /*
        |----------------------------------------------------------------------
        | REFRESH TOKEN
        |----------------------------------------------------------------------
        */

        if ($client->isAccessTokenExpired()) {

            $newToken = $client->fetchAccessTokenWithRefreshToken(
                $user->google_refresh_token
            );

            $existingToken = is_array($user->google_token)
                ? $user->google_token
                : [];

            $token = array_merge(
                $existingToken,
                $newToken
            );

            $user->update([

                'google_token' => $token,
            ]);

            $client->setAccessToken(
                $token
            );
        }

        return $client;
    }

    public function syncEvent(Event $event)
    {
        $user = $event->user;

        if (! $user->google_token) {
            return;
        }
        if ($event->google_event_id) {
            return;
        }

        if (! $event->start_at) {
            return;
        }

        $client = $this->getClient($user);

        $service = new Calendar($client);

        $googleEvent = new GoogleEvent([

            'summary' => $event->title,

            'description' => $event->description,

            'start' => [
                'dateTime' =>
                    $event->start_at->toRfc3339String(),

                'timeZone' =>
                    config('app.timezone'),
            ],

            'end' => [
                'dateTime' =>
                    $event->calculated_end_at
                        ->toRfc3339String(),

                'timeZone' =>
                    config('app.timezone'),
            ],
        ]);

        $createdEvent = $service->events->insert(
            'primary',
            $googleEvent
        );

        $event->update([

            'google_event_id' =>
                $createdEvent->id,
        ]);
    }

    public function updateEvent(Event $event)
    {
        $user = $event->user;
    
        if (
            ! $user->google_token ||
            ! $event->google_event_id
        ) {
            return;
        }
    
        $client = $this->getClient($user);
    
        $service = new Calendar($client);
    
        $googleEvent = $service->events->get(
            'primary',
            $event->google_event_id
        );
    
        $googleEvent->setSummary(
            $event->title
                ?: $event->company
                ?: 'Event'
        );
    
        $googleEvent->setDescription(
            $event->description
        );
    
        $googleEvent->setStart(
            new \Google\Service\Calendar\EventDateTime([
                'dateTime' => $event->start_at
                    ->toRfc3339String(),
            ])
        );
    
        $googleEvent->setEnd(
            new \Google\Service\Calendar\EventDateTime([
                'dateTime' => (
                    $event->end_at
                    ?? $event->start_at->copy()->addHour()
                )->toRfc3339String(),
            ])
        );
    
        $service->events->update(
            'primary',
            $event->google_event_id,
            $googleEvent
        );
    }

    public function deleteEvent($user, $googleEventId)
    {
        $client = $this->getClient($user);
    
        $service = new \Google\Service\Calendar($client);
    
        $service->events->delete(
            'primary',
            $googleEventId
        );
    }
    
    
}