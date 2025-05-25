<h1>New Event Created</h1>
<p><strong>{{ $event->title }}</strong> has been scheduled for {{ $event->start_time->format('F j, Y g:i A') }}.</p>
<p>Description: {{ $event->description }}</p>
<p>Location: {{ $event->location ?? 'Online' }}</p>
