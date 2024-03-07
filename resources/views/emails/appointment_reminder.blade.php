@component('mail::message')

<h1>Üdvözli Önt a VetCareConnect!</h1>
<img src="{{ $message->embed(public_path().'/VetCareConnect_green.png') }}">
Önnek 2 nap múlva időpontja van 12:00 órakor.

@endcomponent
