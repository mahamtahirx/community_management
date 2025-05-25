<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Event;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class SendEventReminders extends Command
{
    protected $signature = 'events:send-reminders';
    protected $description = 'Send reminders to community members a day before events';

    public function handle()
    {
        $tomorrow = Carbon::tomorrow()->toDateString();
        $events = Event::whereDate('start_time', $tomorrow)->with('community.members')->get();

        foreach ($events as $event) {
            foreach ($event->community->members as $member) {
                try {
                    Http::post('http://localhost:3001/send-email', [
                        'to' => $member->email,
                        'subject' => 'Reminder: ' . $event->title . ' is happening tomorrow',
                        'html' => view('emails.event_reminder', ['event' => $event, 'member' => $member])->render(),
                    ]);
                } catch (\Exception $e) {
                    \Log::error("Reminder email failed for {$member->email}: " . $e->getMessage());
                }
            }
        }

        $this->info('Reminder emails sent for ' . $events->count() . ' events.');
    }
}
