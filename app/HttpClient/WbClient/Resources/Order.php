<?php

namespace App\HttpClient\WbClient\Resources;

use App\HttpClient\WbClient\Resources\Card\Card;
use App\HttpClient\WbClient\Resources\Card\CardList;
use App\HttpClient\WbClient\WbClient;
use App\Models\WbMarket;
use Illuminate\Support\Collection;

class Order
{
    const ENDPOINT = 'https://marketplace-api.wildberries.ru/api/v3/orders/new';

    const ATTRIBUTES = [
        "Основная информация" => [
            ["name" => "ddate", "label" => "Планируемая дата доставки"],
            ["name" => "salePrice", "label" => "Цена в валюте продажи с учётом скидки продавца, без учёта скидки WB Клуба, умноженная на 100"],
            ["name" => "dTimeFrom", "label" => 'Время доставки "с"'],
            ["name" => "dTimeTo", "label" => 'Время доставки "до"'],
            ["name" => "requiredMeta", "label" => "Перечень метаданных, которые необходимо добавить в сборочное задание"],
            ["name" => "deliveryType", "label" => "Тип доставки"],
            ["name" => "comment", "label" => "Комментарий покупателя"],
            ["name" => "scanPrice", "label" => "Цена приёмки в копейках"],
            ["name" => "orderUid", "label" => "ID транзакции для группировки сборочных заданий"],
            ["name" => "article", "label" => "Артикул продавца"],
            ["name" => "colorCode", "label" => "Код цвета (только для колеруемых товаров)"],
            ["name" => "rid", "label" => "ID сборочного задания в системе Wildberries"],
            ["name" => "createdAt", "label" => "Дата создания сборочного задания (RFC3339)"],
            ["name" => "offices", "label" => "Список офисов, куда следует привезти товар"],
            ["name" => "skus", "label" => "Массив баркодов товара"],
            ["name" => "id", "label" => "ID сборочного задания в Маркетплейсе"],
            ["name" => "warehouseId", "label" => "ID склада продавца, на который поступило сборочное задание"],
            ["name" => "nmId", "label" => "Артикул WB"],
            ["name" => "chrtId", "label" => "ID размера товара в системе Wildberries"],
            ["name" => "price", "label" => "Цена в валюте продажи с учётом всех скидок, кроме суммы по WB Кошельку, умноженная на 100"],
            ["name" => "convertedPrice", "label" => "Цена в валюте страны продавца с учетом всех скидок, кроме суммы по WB Кошельку, умноженная на 100"],
            ["name" => "currencyCode", "label" => "Код валюты продажи (ISO 4217)"],
            ["name" => "convertedCurrencyCode", "label" => "Код валюты страны продавца (ISO 4217)"],
            ["name" => "cargoType", "label" => "Тип товара"],
            ["name" => "isZeroOrder", "label" => "Признак заказа сделанного на нулевой остаток товара"],
        ],
        "Детализованный адрес покупателя для доставки" => [
            ["name" => "address_fullAddress", "label" => "Адрес доставки"],
            ["name" => "address_longitude", "label" => "Координата долготы"],
            ["name" => "address_latitude", "label" => "Координаты широты"],
        ]
    ];

    protected ?string $address_fullAddress;
    protected ?float $address_longitude;
    protected ?float $address_latitude;
    protected ?string $ddate;
    protected ?int $salePrice;
    protected ?string $dTimeFrom;
    protected ?string $dTimeTo;
    protected ?Collection $requiredMeta;
    protected ?string $deliveryType;
    protected ?string $comment;
    protected ?int $scanPrice;
    protected ?string $orderUid;
    protected ?string $article;
    protected ?string $colorCode;
    protected ?string $rid;
    protected ?string $createdAt;
    protected ?Collection $offices;
    protected ?Collection $skus;
    protected ?int $id;
    protected ?int $warehouseId;
    protected ?int $nmId;
    protected ?int $chrtId;
    protected ?int $price;
    protected ?int $convertedPrice;
    protected ?int $currencyCode;
    protected ?int $convertedCurrencyCode;
    protected ?int $cargoType;
    protected ?bool $isZeroOrder;
    protected ?Card $card;

    public function __construct(Collection $order = null)
    {
        if ($order) {
            $this->address_fullAddress = $order->get('address')?->get('fullAddress');
            $this->address_longitude = $order->get('address')?->get('longitude');
            $this->address_latitude = $order->get('address')?->get('latitude');
            $this->ddate = $order->get('ddate');
            $this->salePrice = $order->get('salePrice');
            $this->dTimeFrom = $order->get('dTimeFrom');
            $this->dTimeTo = $order->get('dTimeTo');
            $this->requiredMeta = $order->get('requiredMeta');
            $this->deliveryType = $order->get('deliveryType');
            $this->comment = $order->get('comment');
            $this->scanPrice = $order->get('scanPrice');
            $this->orderUid = $order->get('orderUid');
            $this->article = $order->get('article');
            $this->colorCode = $order->get('colorCode');
            $this->rid = $order->get('rid');
            $this->createdAt = $order->get('createdAt');
            $this->offices = $order->get('offices');
            $this->skus = $order->get('skus');
            $this->id = $order->get('id');
            $this->warehouseId = $order->get('warehouseId');
            $this->nmId = $order->get('nmId');
            $this->chrtId = $order->get('chrtId');
            $this->price = $order->get('price');
            $this->convertedPrice = $order->get('convertedPrice');
            $this->currencyCode = $order->get('currencyCode');
            $this->convertedCurrencyCode = $order->get('convertedCurrencyCode');
            $this->cargoType = $order->get('cargoType');
            $this->isZeroOrder = $order->get('isZeroOrder');
        }
    }

    public function fetchCard(string $api_key): void
    {
        $list = new CardList($api_key);
        $list->setFilterTextSearch($this->article);
        $cards = $list->next();
        $this->card = $cards->first();
    }

    public function getNewAll(string $api_key): Collection
    {
        $client = new WbClient($api_key);

        $response = $client->get(self::ENDPOINT);

        return $response->collect()->toCollectionSpread()->get('orders')->map(fn (Collection $order) => new self($order));
    }

    public function getDeliveryType(): string
    {
        return match ($this->deliveryType) {
            "fbs" => "доставка на склад Wildberries",
            "dbs" => "доставка силами продавца",
            "edbs" => "экспресс-доставка силами продавца",
            "wbgo" => "доставка курьером WB",
            default => "неизвестно",
        };
    }

    public function getCargoType(): string
    {
        return match ($this->cargoType) {
            1 => "обычный",
            2 => "СГТ (Сверхгабаритный товар)",
            3 => "КГТ (Крупногабаритный товар)",
            default => "неизвестно",
        };
    }

    public function toCollection(WbMarket $market): Collection
    {
        $this->fetchCard($market->api_key);

        return collect([
            'address_fullAddress' => $this->address_fullAddress,
            'address_longitude' => $this->address_longitude,
            'address_latitude' => $this->address_latitude,
            'ddate' => $this->ddate,
            'salePrice' => $this->salePrice,
            'dTimeFrom' => $this->dTimeFrom,
            'dTimeTo' => $this->dTimeTo,
            'requiredMeta' => $this->requiredMeta,
            'deliveryType' => $this->getDeliveryType(),
            'comment' => $this->comment,
            'scanPrice' => $this->scanPrice,
            'orderUid' => $this->orderUid,
            'article' => $this->article,
            'colorCode' => $this->colorCode,
            'rid' => $this->rid,
            'createdAt' => $this->createdAt,
            'offices' => $this->offices,
            'skus' => $this->skus,
            'id' => $this->id,
            'warehouseId' => $this->warehouseId,
            'nmId' => $this->nmId,
            'chrtId' => $this->chrtId,
            'price' => $this->price,
            'convertedPrice' => $this->convertedPrice,
            'currencyCode' => $this->currencyCode,
            'convertedCurrencyCode' => $this->convertedCurrencyCode,
            'cargoType' => $this->getCargoType(),
            'isZeroOrder' => $this->isZeroOrder,
            'card' => $this->card->toCollection($market),
        ]);
    }
}
