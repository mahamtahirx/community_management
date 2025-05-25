<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Event Reminder</title>
</head>
<body>
    <h2>Hello {{ $member->name }},</h2>
    <p>This is a reminder that you have an upcoming event:</p>
    <ul>
        <li><strong>Title:</strong> {{ $event->title }}</li>
        <li><strong>When:</strong> {{ $event->start_time->format('F j, Y g:i A') }}</li>
        <li><strong>Location:</strong> {{ $event->location ?? 'TBA' }}</li>
    </ul>
    <p>See you there!</p>
</body>
</html>
