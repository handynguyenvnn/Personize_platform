@component('mail::message')

adminさんへ
{{ $data['name'] }}さんからの問い合わせメールが届くました。

件名
{{ $data['subject'] }}関するお問い合わせ

{{ $data['message'] }}

{{ $data['date'] }}
以上、ご回答いただけますようよろしくお願いいたします。

よろしくお願いします<br>
{{ config('app.name') }}
@endcomponent