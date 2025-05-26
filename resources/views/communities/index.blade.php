@extends('layouts.app')

@section('content')
    <div class="container mt-5">
        <h2>Communities</h2>
        <a href="{{ route('communities.create.form') }}" class="btn btn-primary mb-3">Create Community</a>

        @if ($communities->count())
            <div class="row">
                @foreach ($communities as $community)
                    <div class="col-md-4 mb-4">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title"><a href="{{ route('communities.show', $community->id) }}" class="text-decoration-none text-dark">{{ $community->name }}</a></h5>
                                <p class="card-text">{{ $community->description }}</p>
                                @auth
                                    @php
                                        $membership = $community->members->firstWhere('id', auth()->id());
                                    @endphp

                                    @if ($membership)
                                        <span class="badge bg-success">Joined as {{ $membership->pivot->role }}</span>
                                    @else
                                        <form action="{{ route('communities.join', $community) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-outline-primary">Join</button>
                                        </form>
                                    @endif
                                @endauth
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p>No communities available.</p>
        @endif
    </div>
@endsection