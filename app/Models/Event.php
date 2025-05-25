<?php

  namespace App\Models;

  use Illuminate\Database\Eloquent\Model;

  class Event extends Model
  {
      protected $fillable = [
          'community_id',
          'title',
          'description',
          'start_time',
          'created_by',
      ];
      protected $casts = [
    'start_time' => 'datetime',
    'end_time' => 'datetime',
];


      public function community()
      {
          return $this->belongsTo(Community::class);
      }

      public function rsvps()
      {
          return $this->hasMany(RSVP::class);
      }
      public function creator()
{
    return $this->belongsTo(User::class, 'created_by');
}

public function attendingUsers()
{
    return $this->belongsToMany(User::class, 'rsvps')
        ->wherePivot('status', 'attending');
}

public function notAttendingUsers()
{
    return $this->belongsToMany(User::class, 'rsvps')
        ->wherePivot('status', 'not_attending');
}
  }