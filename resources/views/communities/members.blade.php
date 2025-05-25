@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Members of {{ $community->name }}</h2>
        <a href="{{ route('communities.show', $community) }}" class="btn btn-outline-secondary">
            Back to Community
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        @can('manageMembers', $community)
                            <th>Actions</th>
                        @endcan
                    </tr>
                </thead>
                <tbody>
                    @foreach($members as $member)
                        <tr>
                            <td>{{ $member->name }}</td>
                            <td>{{ $member->email }}</td>
                            <td>
                                <span class="badge 
                                    @if($member->pivot->role === 'admin') bg-danger
                                    @elseif($member->pivot->role === 'organizer') bg-warning text-dark
                                    @else bg-primary
                                    @endif">
                                    {{ $member->pivot->role }}
                                </span>
                            </td>
                            @can('manageMembers', $community)
                                <td>
                                    @if($member->pivot->role !== 'admin')
                                        <form 
                                            action="{{ route('communities.update-role', [$community, $member]) }}" 
                                            method="POST" 
                                            class="d-inline">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="role" value="organizer">
                                            <button type="submit" class="btn btn-sm btn-outline-warning">
                                                Make Organizer
                                            </button>
                                        </form>
                                        
                                        <form 
                                            action="{{ route('communities.update-role', [$community, $member]) }}" 
                                            method="POST" 
                                            class="d-inline">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="role" value="member">
                                            <button type="submit" class="btn btn-sm btn-outline-primary">
                                                Make Member
                                            </button>
                                        </form>
                                        
                                        <form 
                                            action="{{ route('communities.remove-member', [$community, $member]) }}" 
                                            method="POST" 
                                            class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                Remove
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            @endcan
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection