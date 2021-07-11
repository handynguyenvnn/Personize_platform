@component('mail::message')
<!-- # Introduction -->

{{ $user_name }}　様

出金依頼は拒否されました。

<!-- @component('mail::button', ['url' => ''])
Button Text
@endcomponent -->

よろしくお願いします<br>
{{ config('app.name') }}
@endcomponent
