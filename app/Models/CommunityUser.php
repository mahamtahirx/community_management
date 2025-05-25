<?php

  namespace App\Models;

  use Illuminate\Database\Eloquent\Relations\Pivot;

  class CommunityUser extends Pivot
  {
      protected $table = 'community_user';

      protected $fillable = [
          'user_id',
          'community_id',
          'role',
      ];

      public function user()
      {
          return $this->belongsTo(User::class);
      }

      public function community()
      {
          return $this->belongsTo(Community::class);
      }
  }