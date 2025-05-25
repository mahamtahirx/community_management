@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Events for {{ $community->name }}</h2>
        @can('createEvent', $community)
            <a href="{{ route('events.create.form', $community) }}" class="btn btn-primary">
                Create Event
            </a>
        @endcan
    </div>

    @if ($events->count())
        <div class="row">
            @foreach ($events as $event)
                <div class="col-md-4 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">
                                <a href="{{ route('events.show', ['community' => $community, 'event' => $event]) }}">
                                    {{ $event->title }}
                                </a>
                            </h5>
                            <p class="card-text">{{ Str::limit($event->description, 100) }}</p>
                            <p class="card-text">
                                <small class="text-muted">
                                    {{ $event->start_time->format('M j, Y g:i A') }}
                                </small>
                            </p>
                            <p class="card-text">
                                <span class="badge bg-info">
                                    {{ $event->rsvps()->where('status', 'attending')->count() }} attending
                                </span>
                            </p>
                            
                            @auth
                                @if (!$event->rsvps()->where('user_id', Auth::id())->exists())
                                    <form action="{{ route('events.rsvp', $event) }}" method="POST">
                                        @csrf
                                        <select name="status" class="form-select mb-2" required>
                                            <option value="attending">Attending</option>
                                            <option value="not_attending">Not Attending</option>
                                        </select>
                                        <button type="submit" class="btn btn-outline-primary">RSVP</button>
                                    </form>
                                @else
                                    <span class="badge bg-info">
                                        {{ $event->rsvps()->where('user_id', Auth::id())->first()->status }}
                                    </span>
                                @endif
                            @endauth
                            
                            @can('update', $event)
                                <a href="{{ route('events.edit', ['community' => $community, 'event' => $event]) }}" 
                                   class="btn btn-sm btn-warning mt-2">
                                    Edit
                                </a>
                            @endcan
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="alert alert-info">No events available.</div>
    @endif
</div>
@endsection