@component('mail::message')
# Hi, Welcome {{ $user['name'] }}!

To verify your email address ({{ $user['email'] }}), please click the following link.

<br />
<a href="{{ $user['activation_code']}}">Verify Email</a>
<br />
<br />
{{ $user['activation_code']}}

Thanks,<br>
MyNearShops
@endcomponent