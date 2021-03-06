<!DOCTYPE html>
<html lang="en-US">
<head>
    <meta charset="utf-8">
</head>
<body>
<p>
    Dear {!! $owner_fullname !!}
</p>
<p>
    Your Reservation for room {!! $room_complete_name !!} got canceled because you did not take any action to pay it.
</p>
<p>
<ul>
    <li>Id {!! $reservation_id !!}</li>
    <li>Owner {!! $owner_fullname!!}</li>
    <li>Email {!! $owner_email!!}</li>
    <li>From {!! $reservation_start_datetime !!}</li>
    <li>To {!! $reservation_end_datetime !!}</li>
    <li>Created {!!$reservation_created_datetime !!}</li>
    <li>Amount {!! $reservation_currency !!} {!! $reservation_amount !!}</li>
</ul>
</p>
<p>Cheers,<br/>{!! Config::get('app.tenant_name') !!} Support Team</p>
</body>
</html>
