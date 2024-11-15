<?php

namespace App\HttpClient\WbClient\Resources;

use App\Helpers\Helpers;
use App\HttpClient\WbClient\Resources\Card\Card;
use App\HttpClient\WbClient\Resources\Card\CardList;
use App\HttpClient\WbClient\WbClient;
use App\Models\User;
use App\Models\WbMarket;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Livewire\Wireable;

class Order implements Wireable
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
    protected ?Card $card = null;
    protected ?Sticker $sticker = null;
    public function __construct(Collection $order = null)
    {
        if ($order) {
            $this->address_fullAddress = $order->get('address') ? $order->get('address')->get('fullAddress') : $order->get('address_fullAddress');
            $this->address_longitude = $order->get('address') ? $order->get('address')->get('longitude') : $order->get('address_longitude');
            $this->address_latitude = $order->get('address') ? $order->get('address')->get('latitude') : $order->get('address_latitude');
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
            if ($order->has('card')) {
                $this->card = $order->get('card');
            }
            if ($order->has('sticker')) {
                $this->sticker = $order->get('sticker');
            }
        }
    }

    public function fetchCard(string $api_key): void
    {
        $list = new CardList($api_key);
        $list->setFilterTextSearch($this->article);
        $cards = $list->next();
        $cards->each(function (Card $card) {
            if ($card->getVendorCode() === $this->article) {
                $this->card = $card;
            }
        });
    }

    public function getNewAll(WbMarket $market): Collection
    {
        $client = new WbClient($market->api_key);

        $response = $client->get(self::ENDPOINT);

        return $response->collect()->toCollectionSpread()->get('orders')->map(function (Collection $order) use ($market) {
            return new self($order);
        });
    }

    public static function setDeliveryType($value): string
    {
        return match ($value) {
            "доставка на склад Wildberries" => "fbs",
            "доставка силами продавца" => "dbs",
            "экспресс-доставка силами продавца" => "edbs",
            "доставка курьером WB" => "wbgo",
            default => null,
        };
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

    public static function setCargoType($value): int
    {
        return match ($value) {
            "обычный" => 1,
            "СГТ (Сверхгабаритный товар)" => 2,
            "КГТ (Крупногабаритный товар)" => 3,
            default => null,
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

    public function toLivewire(): array
    {
        return [
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
            'card' => $this->card ? $this->card->toLivewire() : null,
            'sticker' => $this->sticker ? $this->sticker->toLivewire() : null,
        ];
    }

    public static function fromLivewire($value): Order
    {
        $data = collect($value)->toCollectionSpread();

        $data['deliveryType'] = static::setDeliveryType($data['deliveryType']);
        $data['cargoType'] = static::setCargoType($data['cargoType']);

        if ($data['card']) $data['card'] = new Card($data->get('card'));
        if ($data['sticker']) $data['sticker'] = new Sticker($data->get('sticker'));
        if ($data['user']) $data['user'] = User::find($data['user']['id']);

        return new static($data);
    }

    public function setSticker(Sticker $sticker): void
    {
        $this->sticker = $sticker;
    }

    public function getAddressFullAddress(): ?string
    {
        return $this->address_fullAddress;
    }

    public function getAddressLongitude(): ?float
    {
        return $this->address_longitude;
    }

    public function getAddressLatitude(): ?float
    {
        return $this->address_latitude;
    }

    public function getDdate(): ?string
    {
        return $this->ddate;
    }

    public function getSalePrice(): ?int
    {
        return $this->salePrice;
    }

    public function getDTimeFrom(): ?string
    {
        return $this->dTimeFrom;
    }

    public function getDTimeTo(): ?string
    {
        return $this->dTimeTo;
    }

    public function getRequiredMeta(): ?Collection
    {
        return $this->requiredMeta;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function getScanPrice(): ?int
    {
        return $this->scanPrice;
    }

    public function getOrderUid(): ?string
    {
        return $this->orderUid;
    }

    public function getArticle(): ?string
    {
        return $this->article;
    }

    public function getColorCode(): ?string
    {
        return $this->colorCode;
    }

    public function getRid(): ?string
    {
        return $this->rid;
    }

    public function getCreatedAt(User $user): ?string
    {
        return Carbon::createFromFormat('Y-m-dTH:i:sZ', $this->createdAt)->setTimezone(Helpers::getUserTimeZone($user))->format('Y-m-d H:i:s');
    }

    public static function setCreatedAt($value): string
    {
        return Carbon::createFromFormat('Y-m-d H:i:s', $value)->setTimezone('UTC')->format('Y-m-dTH:i:sZ');
    }

    public function getOffices(): ?Collection
    {
        return $this->offices;
    }

    public function getSkus(): ?Collection
    {
        return $this->skus;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getWarehouseId(): ?int
    {
        return $this->warehouseId;
    }

    public function getNmId(): ?int
    {
        return $this->nmId;
    }

    public function getChrtId(): ?int
    {
        return $this->chrtId;
    }

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function getConvertedPrice(): ?int
    {
        return $this->convertedPrice;
    }

    public function getCurrencyCode(): ?int
    {
        return $this->currencyCode;
    }

    public function getConvertedCurrencyCode(): ?int
    {
        return $this->convertedCurrencyCode;
    }

    public function getIsZeroOrder(): ?bool
    {
        return $this->isZeroOrder;
    }

    public function getCard(): ?Card
    {
        return $this->card;
    }

    public function getSticker(): ?Sticker
    {
        return $this->sticker;
    }


}
