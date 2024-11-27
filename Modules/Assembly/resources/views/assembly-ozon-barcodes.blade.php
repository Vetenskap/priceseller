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
            width: 12cm;
            height: 7.5cm;
            text-align: center;
        }

        .info {
            padding-top: 0.5cm
        }
    </style>
</head>
<body>
<div>
    @foreach($barcodes as $index => $barcode)
        @foreach($barcode->get('carriages') as $carriage)
            <div wire:key="{{$index}}" class="barcode">
                <p class="info">{{$barcode->get('market')->name}}</p>
                <p>Склад: {{$carriage->getWarehouseName()}}</p>
                <p>Количество отправлений: {{$carriage->getCarriagePostingsCount()}}</p>
                <br/>
                <img src="data:image/png;base64, {{$barcode->getActBarcode()->getFileContent()}}"/>
                <br/>
                <p>{{$barcode->getActBarcode()->getText()}}</p>
            </div>
        @endforeach
    @endforeach
</div>
</body>
<script>
    window.print();
</script>
</html>
