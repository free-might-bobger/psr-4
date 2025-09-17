@component('mail::message')
# Hi, {{ $inviteInfo['storeName'] }} invited to have access on the store!

To complete the invitation please click following link.

<br />
<a href="{{ $inviteInfo['activation_code'] }}">Accept invitation</a>
<br />
<br />
{{ $inviteInfo['activation_code'] }}

Thanks,<br>
Rocee Stores
@endcomponent