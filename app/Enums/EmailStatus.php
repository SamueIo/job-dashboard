<?php

namespace App\Enums;

enum EmailStatus: string
{
    case APPLIED = 'applied';
    case INTERVIEW = 'interview';
    case OFFER = 'offer';
    case REJECTED = 'rejected';
    case OTHER = 'other';
}