<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Стикеры</title>
    <style>
        @page {
            size: 58mm 40mm;
            margin: 0;
        }
        .sticker {
            width: 58mm; height: 40mm; display: block; page-break-after: always; text-align: center; line-height: 40mm;
        }
    </style>
</head>
<body>
<div>
    @foreach($orders as $order)
        <img src="data:image/svg+xml;base64, {{$order->getSticker()->getFile()}}" width="58" height="40" class="sticker"/>
    @endforeach
</div>
</body>
<script>
    window.print();
</script>
</html>
