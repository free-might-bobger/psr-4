@component('mail::message')
# Hi, {{ $storeUser['storeName'] }} invited you to have access on the store!

To complete the invitation please click following link.

<br />
<a href="{{ $storeUser['verification_code'] }}">Accept invitation</a>
<br />
<br />
{{ $storeUser['verification_code'] }}

Thanks,<br>
My Near Shops
@endcomponent