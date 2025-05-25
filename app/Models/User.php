<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the communities that the user belongs to.
     */
    public function communities()
    {
        return $this->belongsToMany(Community::class, 'community_user')
                    ->withPivot('role')
                    ->using(CommunityUser::class)
                    ->withTimestamps();
    }

    /**
     * Get the events created by the user.
     */
    public function events()
    {
        return $this->hasMany(Event::class, 'created_by');
    }

    /**
     * Get the user's RSVPs.
     */
    public function rsvps()
    {
        return $this->hasMany(Rsvp::class);
    }
}
