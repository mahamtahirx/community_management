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
use Illuminate\Support\Facades\Http;

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
$members = $community->members()->get();
       

try {
    foreach ($members as $member) {
        Http::post('http://localhost:3001/send-email', [
            'to' => $member->email,
            'subject' => 'New Event: ' . $event->title,
            'html' => view('emails.event_created', compact('event'))->render(),
        ]);
    }
    return redirect()->route('events.index', $community)->with('success', 'Event created and email notifications sent!');
} catch (\Exception $e) {
    Log::error('Email error: ' . $e->getMessage());
    return redirect()->route('events.index', $community)->with('error', 'Event created but email failed.');
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
    public function edit(Community $community, Event $event)
{
    $this->authorize('update', $event);
    return view('events.edit', compact('community', 'event'));
}
public function update(Request $request, Community $community, Event $event)
{
    $this->authorize('update', $event);

    $request->validate([
        'title' => 'required|string|max:255',
        'description' => 'nullable|string',
        'start_time' => 'required|date',
        'end_time' => 'nullable|date|after:start_time',
        'location' => 'nullable|string',
    ]);

    $event->update($request->only([
        'title', 'description', 'start_time', 'end_time', 'location'
    ]));

    return redirect()->route('events.show', [$community, $event])
        ->with('success', 'Event updated successfully.');
}
public function destroy(Community $community, Event $event)
{
    $this->authorize('delete', $event);
    $event->delete();

    return redirect()->route('events.index', $community)
        ->with('success', 'Event deleted successfully.');
}
public function sendReminder(Community $community, Event $event)
{
    $this->authorize('update', $event);

    if ($event->reminder_sent_at) {
        return redirect()->route('events.show', [$community, $event])
            ->with('error', 'Reminder has already been sent.');
    }

    try {
        $members = $community->members()->get();

        foreach ($members as $member) {
            Http::post('http://localhost:3001/send-email', [
                'to' => $member->email,
                'subject' => 'Reminder: ' . $event->title . ' is coming soon!',
                'html' => view('emails.event_reminder', compact('event', 'member'))->render(),
            ]);
        }

        $event->reminder_sent_at = now();
        $event->save();

        return redirect()->route('events.show', [$community, $event])
            ->with('success', 'Reminder emails sent to all members.');
    } catch (\Exception $e) {
        \Log::error("Reminder email error: " . $e->getMessage());

        return redirect()->route('events.show', [$community, $event])
            ->with('error', 'Failed to send reminder emails.');
    }
}



    
}
