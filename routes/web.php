<?php

use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\GmailController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmailController;
use App\Http\Controllers\CalendarController;

Route::inertia('/', 'Welcome', [
    'canRegister' => Features::enabled(Features::registration()),
])->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/emails/{id}', [GmailController::class, 'show']);
    Route::post('/sync-emails', [GmailController::class, 'syncEmails']);

    Route::get('/emails', [EmailController::class, 'index'])
        ->name('emails.index');
    Route::get('/api/emails', [EmailController::class, 'filter']);
    Route::patch('/emails/{email}/seen',[EmailController::class, 'markAsSeen'])->name('emails.seen');

    Route::get('/calendar', [CalendarController::class, 'index'])->name('calendar');
    Route::post('/calendar/google-sync',[CalendarController::class, 'toggleGoogleSync']);
    Route::post('/calendar/sync',[CalendarController::class, 'sync']);
    Route::post('/calendar/events/{event}/sync',[CalendarController::class, 'syncEvent']);
    Route::post('/calendar/events', [CalendarController::class, 'store']);
    Route::put('/calendar/events/{event}',[CalendarController::class, 'update']);
    Route::patch('calendar/events/{event}/hide', [CalendarController::class, 'hide']);
    Route::delete('/calendar/events/{event}/force', [CalendarController::class, 'forceDelete']);

});

Route::get('/auth/google', [GoogleController::class, 'redirect']);
Route::get('/auth/google/callback', [GoogleController::class, 'callback']);

require __DIR__.'/settings.php';
