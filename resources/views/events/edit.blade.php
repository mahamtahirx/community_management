@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h2>Edit Event for {{ $community->name }}</h2>
    <form action="{{ route('events.update', [$community, $event]) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="title" class="form-label">Event Title</label>
            <input type="text" name="title" id="title" class="form-control"
                   value="{{ old('title', $event->title) }}" required>
        </div>

        <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea name="description" id="description" class="form-control" rows="4">{{ old('description', $event->description) }}</textarea>
        </div>

        <div class="mb-3">
            <label for="start_time" class="form-label">Start Time</label>
            <input type="datetime-local" name="start_time" id="start_time" class="form-control"
                   value="{{ old('start_time', $event->start_time->format('Y-m-d\TH:i')) }}" required>
        </div>

        <div class="mb-3">
            <label for="end_time" class="form-label">End Time</label>
            <input type="datetime-local" name="end_time" id="end_time" class="form-control"
                   value="{{ old('end_time', optional($event->end_time)->format('Y-m-d\TH:i')) }}">
        </div>

        <div class="mb-3">
            <label for="location" class="form-label">Location</label>
            <input type="text" name="location" id="location" class="form-control"
                   value="{{ old('location', $event->location) }}">
        </div>

        <button type="submit" class="btn btn-primary">Update Event</button>
        <a href="{{ route('events.show', [$community, $event]) }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection
