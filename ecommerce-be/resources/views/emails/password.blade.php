@component('mail::message')
# Hi, {{ $array['name'] }}!

To reset your password on this email ({{ $array['email'] }}), please click the following link.

<br />
<a href="{{ $array['activation_code']}}">Reset Email</a>
<br />
<br />
{{ $array['activation_code']}}

Thanks,<br>
Saint Homobonus Academy, Inc.
@endcomponent