<?php

namespace App\Policies;

use App\Models\Community;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CommunityPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can create events in the community.
     */
    public function createEvent(User $user, Community $community)
    {
        $role = $community->members()
            ->where('user_id', $user->id)
            ->first()
            ->pivot
            ->role ?? null;
            
        return in_array($role, ['admin', 'organizer']);
    }

    /**
     * Determine whether the user can view the members list.
     */
    public function viewMembers(User $user, Community $community)
    {
        return $community->members()
            ->where('user_id', $user->id)
            ->exists();
    }

    /**
     * Determine whether the user can manage members (change roles, remove).
     */
    public function manageMembers(User $user, Community $community)
    {
        $role = $community->members()
            ->where('user_id', $user->id)
            ->first()
            ->pivot
            ->role ?? null;
            
        return $role === 'admin';
    }

    /**
     * Determine whether the user can update the community.
     */
    public function update(User $user, Community $community)
    {
        return $community->created_by === $user->id || 
               $community->members()
                   ->where('user_id', $user->id)
                   ->where('role', 'admin')
                   ->exists();
    }

    /**
     * Determine whether the user can delete the community.
     */
    public function delete(User $user, Community $community)
    {
        return $community->created_by === $user->id;
    }
}