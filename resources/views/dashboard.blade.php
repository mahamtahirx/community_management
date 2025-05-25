@extends('layouts.app')

  @section('content')
      <div class="container mt-5">
          <h2 class="mb-4">Welcome, {{ Auth::user()->name }}</h2>
          @if (session('success'))
              <div class="alert alert-success">{{ session('success') }}</div>
          @endif
          @if (session('notifications') && count(session('notifications')))
              <div class="alert alert-info">
                  <h4 class="mb-3">Notifications</h4>
                  <ul class="list-group">
                      @foreach (session('notifications') as $notification)
                          <li class="list-group-item">{{ $notification['message'] }}</li>
                      @endforeach
                  </ul>
              </div>
          @endif
          <h3 class="mt-4 mb-3">Your Communities</h3>
          @if ($userCommunities->count())
              <div class="row">
                  @foreach ($userCommunities as $community)
                      <div class="col-md-4 mb-4">
                          <div class="card">
                              <div class="card-body">
                                  <h5 class="card-title">
                                      <a href="{{ route('events.index', $community) }}">{{ $community->name }}</a>
                                  </h5>
                                  <p class="card-text">Role: {{ $community->pivot->role }}</p>
                              </div>
                          </div>
                      </div>
                  @endforeach
              </div>
          @else
              <p>You haven't joined any communities yet. <a href="{{ route('communities.index') }}">Explore communities</a>.</p>
          @endif
      </div>
  @endsection