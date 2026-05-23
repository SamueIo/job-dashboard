<?php

namespace App\Http\Controllers;

use App\Jobs\ClassifyEmailJob;
use App\Models\Email;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Inertia\Inertia;
use App\Models\User;

class GmailController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | SYNC EMAILS
    |--------------------------------------------------------------------------
    */

    public function syncEmails()
    {
        $user = Auth::user();

        /*
        |--------------------------------------------------------------------------
        | LAST SYNCED EMAIL
        |--------------------------------------------------------------------------
        */
        if (
            !$user->google_token_expires_at ||
            $user->google_token_expires_at->isPast()
        ) {
            $this->refreshGoogleToken($user);
        
            $user->refresh();
        }

        $lastEmail = Email::query()
            ->where('user_id', $user->id)
            ->whereNotNull('gmail_date')
            ->orderByDesc('gmail_date')
            ->first();

        /*
        |--------------------------------------------------------------------------
        | GMAIL QUERY
        |--------------------------------------------------------------------------
        */

        // First sync → last 14 days
        // Next syncs → only newer emails

        $query = $lastEmail
            ? 'after:' . $lastEmail->gmail_date->timestamp
            : 'newer_than:14d';

        /*
        |--------------------------------------------------------------------------
        | FETCH EMAIL LIST
        |--------------------------------------------------------------------------
        */

        $list = Http::withToken($user->google_token)
            ->get(
                'https://gmail.googleapis.com/gmail/v1/users/me/messages',
                [
                    'maxResults' => 25,
                    'q' => $query,
                ]
            )
            ->json();

        /*
        |--------------------------------------------------------------------------
        | PROCESS EMAILS
        |--------------------------------------------------------------------------
        */
        foreach ($list['messages'] ?? [] as $msg) {
            $detail = Http::withToken($user->google_token)
                ->get(
                    "https://gmail.googleapis.com/gmail/v1/users/me/messages/{$msg['id']}"
                )
                ->json();

            $headers = collect(
                $detail['payload']['headers'] ?? []
            );

            $email = Email::updateOrCreate(

                [
                    'gmail_id' => $msg['id'],
                    'user_id' => $user->id,
                ],

                [
                    'subject' => $this->getHeader(
                        $headers,
                        'subject'
                    ),

                    'from' => $this->getHeader(
                        $headers,
                        'from'
                    ),

                    'snippet' => $detail['snippet'] ?? null,

                    'body' => $this->extractBody(
                        $detail['payload'] ?? null
                    ),

                    'gmail_date' => !empty($detail['internalDate'])
                        ? Carbon::createFromTimestampMs(
                            $detail['internalDate']
                        )
                        : null,
                ]
            );

            /*
            |--------------------------------------------------------------------------
            | CLASSIFY
            |--------------------------------------------------------------------------
            */

            if (
                $email->wasRecentlyCreated ||
                !$email->status ||
                !$email->priority
            ) {
                ClassifyEmailJob::dispatch($email->id);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | RECHECK LAST EMAILS
        |--------------------------------------------------------------------------
        */

        $recentEmails = Email::query()
            ->where('user_id', $user->id)
            ->latest()
            ->orderByDesc('gmail_date')
            ->take(10)
            ->get();

        foreach ($recentEmails as $email) {

            if (
                !$email->status ||
                !$email->priority
            ) {
                ClassifyEmailJob::dispatch($email->id);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | RESPONSE
        |--------------------------------------------------------------------------
        */

        return response()->json([
            'success' => true,
            'fetched' => count($list['messages'] ?? []),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | EXTRACT BODY
    |--------------------------------------------------------------------------
    */

    private function extractBody($payload)
    {
        if (!$payload) {
            return null;
        }

        /*
        |--------------------------------------------------------------------------
        | DIRECT BODY
        |--------------------------------------------------------------------------
        */

        if (!empty($payload['body']['data'])) {

            return $this->decodeBody(
                $payload['body']['data']
            );
        }

        /*
        |--------------------------------------------------------------------------
        | MULTIPART BODY
        |--------------------------------------------------------------------------
        */

        foreach ($payload['parts'] ?? [] as $part) {

            if (!empty($part['body']['data'])) {

                return $this->decodeBody(
                    $part['body']['data']
                );
            }
        }

        return null;
    }

    /*
    |--------------------------------------------------------------------------
    | DECODE BODY
    |--------------------------------------------------------------------------
    */

    private function decodeBody($data)
    {
        // base64url → base64

        $data = str_replace(
            ['-', '_'],
            ['+', '/'],
            $data
        );

        return base64_decode($data);
    }

    /*
    |--------------------------------------------------------------------------
    | GET HEADER
    |--------------------------------------------------------------------------
    */

    private function getHeader($headers, $key)
    {
        return collect($headers)
            ->first(
                fn ($h) =>
                    strtolower($h['name']) === strtolower($key)
            )['value'] ?? null;
    }

    /*
    |--------------------------------------------------------------------------
    | SHOW EMAIL
    |--------------------------------------------------------------------------
    */

    public function show($id)
    {
        $user = Auth::user();

        $email = Email::query()
            ->where('user_id', $user->id)
            ->where('gmail_id', $id)
            ->firstOrFail();

        return Inertia::render('Dashboard', [
            'email' => $email,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Get refresh token
    |--------------------------------------------------------------------------
    */

    public function refreshGoogleToken(User $user)
    {
        $response = Http::asForm()->post(
            'https://oauth2.googleapis.com/token',
            [
                'client_id' => config('services.google.client_id'),
                'client_secret' => config('services.google.client_secret'),
                'refresh_token' => $user->google_refresh_token,
                'grant_type' => 'refresh_token',
            ]
        );

        $data = $response->json();

        if (isset($data['access_token'])) {

            $user->update([
                'google_token' => $data['access_token'],

                'google_token_expires_at' => now()->addSeconds(
                    $data['expires_in']
                ),
            ]);

            return true;
        }

        return false;
    }
   
}