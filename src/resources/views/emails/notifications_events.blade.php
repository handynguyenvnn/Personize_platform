<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>nnyouseijuku.com</title>
    <style>
        span{
            display: inline-block;
        }
    </style>
</head>
<body>
    <span>{{$user_name}}様</span><br/>
    <span>株式会社NN養成塾です。</span><br/>
    <span>{{$date_start}}視聴予約して頂いたイベント開始まで、あと15分になりました。</span><br/>
    <span>改めて、本日、具合の確認をさせていただきます。</span><br/>
    <span>＜日時＞</span><br/>
    <span>{{$date_start}} {{$time_start}}</span><br/>
    <span>＜配信を行うサイト＞</span><br/>
    <span>{{$link}}</span><br/>
    <span>＜イベントに関するお問い合わせ</span><br/>
    <span>ご不明点などがございましたら、以下からお問い合わせください。</span><br/>
    <span>{{$contact}}</span><br/>
</body>
</html>