@extends('layouts.app')

@section('content')
    <div class="container mt-5">
        <h2 class="mb-4">Create Event for {{ $community->name }}</h2>
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <form action="{{ route('events.create', $community) }}" method="POST">
            @csrf
            <div class="mb-3">
                <label for="title" class="form-label">Event Title</label>
                <input type="text" class="form-control" id="title" name="title" required>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" id="description" name="description" rows="3"></textarea>
            </div>
            <div class="mb-3">
                <label for="start_time" class="form-label">Start Time</label>
                <input type="datetime-local" class="form-control" id="start_time" name="start_time" required>
            </div>
            <button type="submit" class="btn btn-primary">Create Event</button>
        </form>
    </div>
@endsection