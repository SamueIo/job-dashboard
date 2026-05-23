<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Jobs\SyncGoogleCalendarJob;

class GoogleController extends Controller
{
    public function redirect()
    {
        return Socialite::driver('google')
        ->scopes([
            'openid',
            'profile',
            'email',
            'https://www.googleapis.com/auth/gmail.readonly',
            'https://www.googleapis.com/auth/calendar'
        ])
        ->with([
            'access_type' => 'offline',
            'prompt' => 'consent'
        ])
        ->redirect();
    }

    public function callback()
    {
        $googleUser = Socialite::driver('google')->user();

        $existingUser = User::where(
            'email',
            $googleUser->getEmail()
        )->first();

        $user = User::updateOrCreate(

            [
                'email' => $googleUser->getEmail()
            ],

            [
                'name' => $googleUser->getName(),

                'google_id' => $googleUser->getId(),

                'google_token' => $googleUser->token,

                'google_refresh_token' =>
                    $googleUser->refreshToken
                    ?? $existingUser?->google_refresh_token,

                'google_token_expires_at' =>
                    now()->addSeconds(
                        $googleUser->expiresIn
                    ),

                'password' => bcrypt(str()->random(16)),
            ]
        );

        Auth::login($user);

        // Sync google calendar with db
        SyncGoogleCalendarJob::dispatch($user);

        return redirect('/dashboard');
    }
    
    
}
