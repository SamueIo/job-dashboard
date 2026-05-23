<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Email extends Model
{
    protected $fillable = [
        'gmail_id',
        'user_id',
        'subject',
        'from',
        'snippet',
        'body',
        'status',
        'priority',
        'company',
        'role',
        'stage',
        'interview_at',
        'meeting_link',
        'summary',
        'gmail_date',
        'confidence', 
        'seen',
        'ai_status'
    ];
    protected $casts = [
        'gmail_date' => 'datetime',
        'interview_at' => 'datetime',
        'seen' => 'boolean',
        'confidence' => 'float',
];
}