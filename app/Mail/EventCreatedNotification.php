<?php
namespace App\Mail;

use App\Models\Event;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EventCreatedNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $event;

    public function __construct(Event $event)
    {
        $this->event = $event;
    }

public function build()
{
    return $this->subject('New Event Created: ' . $this->event->title)
                ->replyTo('info.adnansultan@gmail.com')
                ->view('emails.event_created');
}

}
