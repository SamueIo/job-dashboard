<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $fillable = [

        'user_id',
        'email_id',

        'title',
        'description',
        'company',

        'start_at',
        'end_at',

        'type',

        'location',
        'meeting_link',

        'completed',
        'source',
        'google_event_id',
        'category',
        'hidden_at',
    ];

    protected $casts = [

        'start_at' => 'datetime',
        'end_at' => 'datetime',

        'completed' => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | USER
    |--------------------------------------------------------------------------
    */

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /*
    |--------------------------------------------------------------------------
    | EMAIL
    |--------------------------------------------------------------------------
    */

    public function email()
    {
        return $this->belongsTo(Email::class);
    }

    public function getCalculatedEndAtAttribute()
    {
        return $this->end_at
            ?? $this->start_at?->copy()->addHour();
    }
}
