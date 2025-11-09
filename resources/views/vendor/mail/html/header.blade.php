@props(['url'])
<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block;">
@if (trim($slot) === 'Cavosh Cafe')
<img src="LogoCavoshCafe.png" class="logo" alt="Cavosh Logo">
@else
{!! $slot !!}
@endif
</a>
</td>
</tr>
