@component('mail::message')
A users comment was reported by {{ $report->reporter->name }} (#{{ $report->reporter->id }}) for "{{ $report->reason }}".<br />
<br />
<b>Comment ID:</b> {{ $report->comment->id }}<br />
<b>Details:</b> {{ $report->details }}
@endcomponent
