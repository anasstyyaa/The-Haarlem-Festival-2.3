<?php

namespace App\Models\Enums;

enum EventTypeEnum: string 
{
    case Reservation = 'reservation';
    case JazzEvent = 'jazz';
    case DanceEvent = 'dance';
    case KidsEvent = 'kids';
    case Tour = 'tour';
}