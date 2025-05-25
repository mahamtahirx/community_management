<?php
namespace App\Http\Controllers;

use App\Models\Community;
use App\Models\CommunityUser;
use App\Models\Event;
use App\Models\Rsvp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommunityController extends Controller
{
    public function index()
    {
        $communities = Community::withCount('members')->get();
        $userCommunities = Auth::check() ? Auth::user()->communities : collect();
        return view('communities.index', compact('communities', 'userCommunities'));
    }

    public function show(Community $community)
    {
        $members = $community->members()->withPivot('role')->get();
        $upcomingEvents = $community->events()
            ->where('start_time', '>', now())
            ->orderBy('start_time')
            ->get();
            
        $pastEvents = $community->events()
            ->where('start_time', '<=', now())
            ->orderByDesc('start_time')
            ->get();

        $userRole = Auth::user()->communities()
            ->where('community_id', $community->id)
            ->first()->pivot->role ?? null;

        return view('communities.show', compact(
            'community', 
            'members',
            'upcomingEvents',
            'pastEvents',
            'userRole'
        ));
    }

    public function showCreateForm()
    {
        return view('communities.create');
    }

    public function create(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $community = Community::create([
            'name' => $request->name,
            'description' => $request->description,
            'created_by' => Auth::id(),
        ]);

        $community->members()->attach(Auth::id(), ['role' => 'admin']);

        return redirect()->route('communities.index')->with('success', 'Community created successfully!');
    }

    public function join(Request $request, Community $community)
    {
        if ($community->members()->where('user_id', Auth::id())->exists()) {
            return redirect()->route('communities.show', $community)->with('error', 'You are already a member.');
        }

        $community->members()->attach(Auth::id(), ['role' => 'member']);
        return redirect()->route('communities.show', $community)->with('success', 'Joined community successfully!');
    }

    public function members(Community $community)
    {
        $this->authorize('viewMembers', $community);
        
        $members = $community->members()
            ->withPivot('role')
            ->orderBy('name')
            ->get();
            
        return view('communities.members', compact('community', 'members'));
    }
    public function updateRole(Request $request, Community $community, User $user)
{
    $this->authorize('manageMembers', $community);
    
    $request->validate([
        'role' => 'required|in:admin,organizer,member'
    ]);
    
    $community->members()->updateExistingPivot($user->id, [
        'role' => $request->role
    ]);
    
    return back()->with('success', 'Member role updated successfully');
}
}