@component('mail::message')
A users mod was reported by {{ $report->reporter->name }} (#{{ $report->reporter->id }}) for "{{ $report->reason }}".<br />
<br />
<b>Mod ID:</b> {{ $report->mod->id }}<br />
<b>Details:</b> {{ $report->details }}
@endcomponent
