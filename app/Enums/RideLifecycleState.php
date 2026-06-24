<?php

namespace App\Enums;

enum RideLifecycleState: string
{
    case Upcoming = 'upcoming';
    case AwaitingPhotos = 'awaiting_photos';
    case Recap = 'recap';

    public function isPastState(): bool
    {
        return $this !== self::Upcoming;
    }
}
