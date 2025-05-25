@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="row">
        <div class="col-md-8">
            <h2>{{ $community->name }}</h2>
            <p class="lead">{{ $community->description }}</p>
            
            <div class="card mb-4">
                <div class="card-header">
                    <h4>Community Stats</h4>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-4">
                            <h5>Members</h5>
                            <p class="display-6">{{ $community->members()->count() }}</p>
                        </div>
                        <div class="col-md-4">
                            <h5>Upcoming Events</h5>
                            <p class="display-6">{{ $upcomingEvents->count() }}</p>
                        </div>
                        <div class="col-md-4">
                            <h5>Past Events</h5>
                            <p class="display-6">{{ $pastEvents->count() }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex mb-4">
                @can('createEvent', $community)
                    <a href="{{ route('events.create.form', $community) }}" class="btn btn-primary me-2">
                        Create Event
                    </a>
                @endcan
                @can('manageMembers', $community)
                    <a href="{{ route('communities.members', $community) }}" class="btn btn-outline-primary">
                        Manage Members
                    </a>
                @endcan
            </div>

            <h4 class="mb-3">Upcoming Events</h4>
            @if($upcomingEvents->count())
                <div class="list-group mb-4">
                    @foreach($upcomingEvents as $event)
                        <a href="{{ route('events.show', ['community' => $community, 'event' => $event]) }}" 
                           class="list-group-item list-group-item-action">
                            <div class="d-flex w-100 justify-content-between">
                                <h5 class="mb-1">{{ $event->title }}</h5>
                                <small>{{ $event->start_time->diffForHumans() }}</small>
                            </div>
                            <p class="mb-1">{{ Str::limit($event->description, 100) }}</p>
                            <small>Location: {{ $event->location ?? 'Online' }}</small>
                        </a>
                    @endforeach
                </div>
            @else
                <div class="alert alert-info">No upcoming events scheduled.</div>
            @endif

            <h4 class="mb-3">Past Events</h4>
            @if($pastEvents->count())
                <div class="list-group">
                    @foreach($pastEvents as $event)
                        <a href="{{ route('events.show', ['community' => $community, 'event' => $event]) }}" 
                           class="list-group-item list-group-item-action">
                            <div class="d-flex w-100 justify-content-between">
                                <h5 class="mb-1">{{ $event->title }}</h5>
                                <small>{{ $event->start_time->format('M d, Y') }}</small>
                            </div>
                            <p class="mb-1">{{ Str::limit($event->description, 100) }}</p>
                        </a>
                    @endforeach
                </div>
            @else
                <div class="alert alert-info">No past events to display.</div>
            @endif
        </div>
        
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h4>Community Members</h4>
                </div>
                <div class="card-body">
                    <ul class="list-group">
                        @foreach($community->members()->limit(5)->get() as $member)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                {{ $member->name }}
                                <span class="badge bg-primary rounded-pill">
                                    {{ $member->pivot->role }}
                                </span>
                            </li>
                        @endforeach
                    </ul>
                    @if($community->members()->count() > 5)
                        <a href="{{ route('communities.members', $community) }}" class="btn btn-link mt-2">
                            View all members
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection