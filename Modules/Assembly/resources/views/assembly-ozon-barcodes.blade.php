<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Штрихкоды</title>
    <style>
        .barcode {
            width: 12cm; height: 7.5cm; text-align: center;
        }
        .info {
            padding-top: 0.5cm
        }
    </style>
</head>
<body>
<div>
    @foreach($barcodes as $index => $barcode)
        <div wire:key="{{$index}}" class="barcode">
            <p class="info">{{$barcode['market_name']}}</p>
            <p>id: {{$barcode['id']}}, Склад: {{$barcode['warehouse']}}</p>
            <p>Количество отправлений: {{$barcode['postings_count']}}</p>
            <br/>
            <img src="data:image/png;base64, {{$barcode['barcode']}}" />
            <br/>
            <p>{barcode.barcode_text}</p>
        </div>
    @endforeach
</div>
</body>
<script>
    window.print();
</script>
</html>
