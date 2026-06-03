@component('mail::message')
# Hi, {{ $storeUserInfo['storeName'] }} invited to have access on the store!

To complete the invitation please click following link.

<br />
<a href="{{ $storeUserInfo['verification_code'] }}">Accept invitation</a>
<br />
<br />
{{ $storeUserInfo['verification_code'] }}

Thanks,<br>
My Near Shops
@endcomponent