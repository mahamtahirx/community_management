@extends('layouts.app')

@section('content')
    <div class="container mt-5">
        <h2 class="mb-4">Create a New Community</h2>
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <form action="{{ route('communities.create') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label for="name" class="form-label">Community Name</label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" id="description" name="description" rows="3"></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Create Community</button>
        </form>
    </div>
@endsection