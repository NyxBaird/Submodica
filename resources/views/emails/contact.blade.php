@component('mail::message')
    {{ $content['user']->name }} (#{{ $content['user']->id }}) has sent a message!<br />
    <br />
    <b>Subject:</b> {{ $content['subject'] }}<br />
    <b>Body:</b> {{ $content['body'] }}
@endcomponent
