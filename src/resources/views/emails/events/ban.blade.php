@component('mail::message')
<!-- # Introduction -->

{{ $user_name }}　様

ユーザーからの報告により、イベントは禁止されました。

<!-- @component('mail::button', ['url' => ''])
Button Text
@endcomponent -->

よろしくお願いします<br>
{{ config('app.name') }}
@endcomponent
