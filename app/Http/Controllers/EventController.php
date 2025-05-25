<?php

namespace App\Http\Controllers;

use App\Mail\EventCreatedNotification;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Models\Community;
use App\Models\Event;
use App\Models\Rsvp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EventController extends Controller
{
    public function index(Community $community)
    {
        $events = $community->events()
            ->withCount(['rsvps as attending_count' => function($query) {
                $query->where('status', 'attending');
            }])
            ->orderBy('start_time')
            ->get();

        $userRole = Auth::user()->communities()
            ->where('community_id', $community->id)
            ->first()->pivot->role ?? null;

        return view('events.index', compact('events', 'community', 'userRole'));
    }

    public function showCreateForm(Community $community)
    {
        $this->authorize('createEvent', $community);
        return view('events.create', compact('community'));
    }

    public function create(Request $request, Community $community)
    {
        $this->authorize('createEvent', $community);

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_time' => 'required|date',
            'end_time' => 'nullable|date|after:start_time',
            'location' => 'nullable|string',
        ]);

        $event = $community->events()->create([
            'title' => $request->title,
            'description' => $request->description,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'location' => $request->location,
            'created_by' => Auth::id(),
        ]);

        try {
            // Make sure members are retrieved
            $members = $community->members()->get();

            foreach ($members as $member) {
                Mail::to($member->email)
                    ->send(new EventCreatedNotification($event));
            }

            return redirect()
                ->route('events.index', $community)
                ->with('success', 'Event created and email notifications sent successfully!');
        } catch (\Exception $e) {
            Log::error('Email sending failed: ' . $e->getMessage());

            return redirect()
                ->route('events.index', $community)
                ->with('error', 'Event created but email notifications failed.');
        }
    }

    public function show(Community $community, Event $event)
    {
        $rsvps = $event->rsvps()
            ->with('user')
            ->get()
            ->groupBy('status');

        $userRsvp = $event->rsvps()
            ->where('user_id', Auth::id())
            ->first();

        return view('events.show', compact('community', 'event', 'rsvps', 'userRsvp'));
    }

    public function rsvp(Request $request, Event $event)
    {
        $request->validate([
            'status' => 'required|in:attending,not_attending',
        ]);

        Rsvp::updateOrCreate(
            ['user_id' => Auth::id(), 'event_id' => $event->id],
            ['status' => $request->status]
        );

        return redirect()->back()->with('success', 'RSVP updated!');
    }
}
