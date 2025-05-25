<?php
/*
 * This file is part of the Event Management System.
 *
 * (c) Your Name <your.email@example.com>
 */

namespace App\Policies;

use App\Models\Event;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class EventPolicy
{
    use HandlesAuthorization;
    
    public function delete(User $user, Event $event)
    {
        return $event->created_by === $user->id;
    }
}