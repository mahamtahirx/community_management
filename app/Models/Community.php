<?php

  namespace App\Models;

  use Illuminate\Database\Eloquent\Model;

  class Community extends Model
  {
      protected $fillable = [
          'name',
          'description',
          'created_by',
      ];

      public function members()
      {
          return $this->belongsToMany(User::class, 'community_user')
                      ->withPivot('role')
                      ->using(CommunityUser::class);
      }

      public function events()
      {
          return $this->hasMany(Event::class);
      }

      public function organizers()
      {
          return $this->members()->wherePivot('role', 'admin');
      }

      public function createdBy()
      {
          return $this->belongsTo(User::class, 'created_by');
      }
      public function creator()
{
    return $this->belongsTo(User::class, 'created_by');
}

public function upcomingEvents()
{
    return $this->hasMany(Event::class)->where('start_time', '>', now())->orderBy('start_time');
}

public function pastEvents()
{
    return $this->hasMany(Event::class)->where('start_time', '<=', now())->orderByDesc('start_time');
}
  }