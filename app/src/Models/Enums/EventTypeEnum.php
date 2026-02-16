<?php

namespace App\Enums;

enum EventType: string 
{
    case Reservation = 'reservation';
    case JazzEvent = 'jazz';
    case DanceEvent = 'dance';
    case KidsEvent = 'kids';
    case Tour = 'tour';
}