@extends('layouts.app')

@section('content')
<div class="container mt-5">

    {{-- Access Check: Only members can view --}}
    @if (!Auth::user()->communities->contains($community))
        <div class="alert alert-warning">
            <strong>You are not a member of this community.</strong> Please join the community to view event details.
            <form action="{{ route('communities.join', $community) }}" method="POST" class="d-inline ms-2">
                @csrf
                <button class="btn btn-sm btn-primary">Join {{ $community->name }}</button>
            </form>
        </div>
        @return
    @endif

    {{-- Toast Notifications --}}
    @if(session('success') || session('error'))
        <div class="toast-container position-fixed bottom-0 end-0 p-3">
            <div class="toast show text-white {{ session('success') ? 'bg-success' : 'bg-danger' }}" role="alert">
                <div class="d-flex">
                    <div class="toast-body">
                        {{ session('success') ?? session('error') }}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>
        </div>
    @endif

    <div class="row">
        <div class="col-md-8">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2>{{ $event->title }}</h2>
                    <p class="lead">{{ $community->name }}</p>
                </div>
                <div>
                    <span class="badge 
                        @if($event->start_time->isPast()) bg-secondary
                        @else bg-success
                        @endif">
                        {{ $event->start_time->isPast() ? 'Past Event' : 'Upcoming' }}
                    </span>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">Event Details</h5>
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Date & Time:</strong> {{ $event->start_time->format('F j, Y g:i A') }}</p>
                            @if($event->end_time)
                                <p><strong>Ends:</strong> {{ $event->end_time->format('F j, Y g:i A') }}</p>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <p><strong>Location:</strong> {{ $event->location ?? 'Online' }}</p>
                            <p><strong>Created by:</strong> {{ $event->creator->name }}</p>
                        </div>
                    </div>
                    <hr>
                    <h5 class="card-title">Description</h5>
                    <p class="card-text">{{ $event->description }}</p>
                </div>
            </div>

            {{-- RSVP Section (only if event is not past) --}}
            @if (!$event->start_time->isPast())
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">RSVP Status</h5>
                </div>
                <div class="card-body">
                    @if($userRsvp)
                        <div class="alert alert-info">
                            Your current RSVP status:
                            <strong>{{ ucfirst(str_replace('_', ' ', $userRsvp->status)) }}</strong>
                        </div>
                    @endif

                    @unless(Auth::id() === $event->created_by)
                        <form action="{{ route('events.rsvp', $event) }}" method="POST">
                            @csrf
                            <div class="btn-group" role="group">
                                <button type="submit" name="status" value="attending" 
                                    class="btn btn-success @if($userRsvp && $userRsvp->status === 'attending') active @endif">
                                    Attending
                                </button>
                                <button type="submit" name="status" value="not_attending" 
                                    class="btn btn-danger @if($userRsvp && $userRsvp->status === 'not_attending') active @endif">
                                    Not Attending
                                </button>
                            </div>
                        </form>
                    @endunless
                </div>
            </div>
            @endif
        </div>

        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Attendance</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <h6>Attending: {{ $rsvps->get('attending', collect())->count() }}</h6>
                        <div class="progress">
                            <div class="progress-bar bg-success" 
                                 style="width: {{ ($rsvps->get('attending', collect())->count() / max(1, $community->members()->count())) * 100 }}%">
                            </div>
                        </div>
                    </div>
                    <div>
                        <h6>Not Attending: {{ $rsvps->get('not_attending', collect())->count() }}</h6>
                        <div class="progress">
                            <div class="progress-bar bg-danger" 
                                 style="width: {{ ($rsvps->get('not_attending', collect())->count() / max(1, $community->members()->count())) * 100 }}%">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @if($rsvps->get('attending', collect())->count())
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Who's Attending</h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-group">
                            @foreach($rsvps->get('attending', collect())->take(5) as $rsvp)
                                <li class="list-group-item">{{ $rsvp->user->name }}</li>
                            @endforeach
                        </ul>
                        @if($rsvps->get('attending', collect())->count() > 5)
                            <button class="btn btn-link mt-2" data-bs-toggle="modal" data-bs-target="#attendingModal">
                                View all attendees
                            </button>
                        @endif
                    </div>
                </div>
            @endif

            @canany(['update', 'delete'], $event)
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="mb-0">Event Management</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        @can('update', $event)
                            <a href="{{ route('events.edit', [$community, $event]) }}" class="btn btn-warning">
                                ✏️ Edit Event
                            </a>

                            {{-- Show Reminder only to admin or organizer --}}
                            @php
                                $userRole = Auth::user()->communities()->where('community_id', $community->id)->first()->pivot->role ?? null;
                            @endphp
                            @if(in_array($userRole, ['admin', 'organizer']))
                                <form action="{{ route('events.remind', [$community, $event]) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-info" {{ $event->reminder_sent_at ? 'disabled' : '' }}>
                                        {{ $event->reminder_sent_at ? 'Reminder Sent' : '🔔 Remind Members' }}
                                    </button>
                                </form>
                            @endif
                        @endcan

                        @can('delete', $event)
                            <form action="{{ route('events.destroy', [$community, $event]) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this event?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger">
                                    🗑️ Delete Event
                                </button>
                            </form>
                        @endcan
                    </div>
                </div>
            </div>
            @endcanany
        </div>
    </div>
</div>

{{-- Attendee Modal --}}
<div class="modal fade" id="attendingModal" tabindex="-1" aria-labelledby="attendingModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="attendingModalLabel">All Attendees</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <ul class="list-group">
                    @foreach($rsvps->get('attending', collect()) as $rsvp)
                        <li class="list-group-item">{{ $rsvp->user->name }}</li>
                    @endforeach
                </ul>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection
