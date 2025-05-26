@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="text-primary-emphasis">Events for {{ $community->name }}</h2>
        @can('createEvent', $community)
            <a href="{{ route('events.create.form', $community) }}" class="btn btn-success shadow-sm">
                + Create Event
            </a>
        @endcan
    </div>

    @if (session('success'))
        <div class="alert alert-success shadow-sm">{{ session('success') }}</div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger shadow-sm">{{ session('error') }}</div>
    @endif

    @if ($events->count())
        <div class="row">
            @foreach ($events as $event)
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card border-0 shadow-sm h-100 transition-all event-card">
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title text-success-emphasis">
                                <a href="{{ route('events.show', ['community' => $community, 'event' => $event]) }}" class="text-decoration-none text-dark">
                                    {{ $event->title }}
                                </a>
                            </h5>
                            <p class="text-muted mb-2">
                                <i class="bi bi-clock"></i>
                                {{ $event->start_time->format('M j, Y g:i A') }}
                            </p>
                            <p class="card-text">{{ Str::limit($event->description, 100) }}</p>

                            <div class="mt-auto">
                                <p class="mt-2">
                                    <span class="badge bg-info text-dark">
                                        {{ $event->rsvps()->where('status', 'attending')->count() }} attending
                                    </span>
                                </p>

                                @auth
                                    @if ($event->start_time > now())
                                        @if (!$event->rsvps()->where('user_id', Auth::id())->exists())
                                            <form action="{{ route('events.rsvp', $event) }}" method="POST" class="mb-2">
                                                @csrf
                                                <select name="status" class="form-select form-select-sm mb-2" required>
                                                    <option value="attending">✅ Attending</option>
                                                    <option value="not_attending">❌ Not Attending</option>
                                                </select>
                                                <button type="submit" class="btn btn-outline-primary btn-sm w-100">
                                                    RSVP
                                                </button>
                                            </form>
                                        @else
                                            <span class="badge bg-secondary text-light">
                                                You marked as: {{ ucfirst($event->rsvps()->where('user_id', Auth::id())->first()->status) }}
                                            </span>
                                        @endif
                                    @else
                                        <span class="badge bg-dark text-light">This event has ended</span>
                                    @endif
                                @endauth

                                @can('update', $event)
                                    @if ($event->start_time > now())
                                        <div class="d-flex flex-wrap gap-2 mt-3">
                                            <a href="{{ route('events.edit', ['community' => $community, 'event' => $event]) }}" 
                                               class="btn btn-sm btn-warning w-100">
                                                ✏️ Edit
                                            </a>
                                            <form action="{{ route('events.send-reminder', [$community, $event]) }}" method="POST" class="w-100">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-outline-info w-100">
                                                    🔔 Send Reminder
                                                </button>
                                            </form>
                                        </div>
                                    @endif
                                @endcan
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="alert alert-info shadow-sm">No events available.</div>
    @endif
</div>

<style>
    .event-card {
        background-color: #fdfdfb;
        border-left: 4px solid #6B705C;
        border-radius: 10px;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .event-card:hover {
        transform: scale(1.02);
        box-shadow: 0 0 15px rgba(107, 112, 92, 0.2);
    }

    .text-primary-emphasis {
        color: #4F5446;
    }

    .text-success-emphasis {
        color: #6B705C;
    }
</style>
@endsection
