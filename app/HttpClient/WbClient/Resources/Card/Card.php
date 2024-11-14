<?php

namespace App\HttpClient\WbClient\Resources\Card;

use App\Models\WbItem;
use App\Models\WbMarket;
use Illuminate\Support\Collection;
use Livewire\Wireable;

class Card implements Wireable
{
    const ATTRIBUTES = [
        "Основная информация" => [
            ["name" => "nmID", "label" => "Артикул WB"],
            ["name" => "imtID", "label" => "ID карточки товара"],
            ["name" => "nmUUID", "label" => "Внутренний технический ID товара"],
            ["name" => "subjectID", "label" => "ID предмета"],
            ["name" => "vendorCode", "label" => "Артикул продавца"],
            ["name" => "subjectName", "label" => "Название предмета"],
            ["name" => "brand", "label" => "Бренд"],
            ["name" => "title", "label" => "Наименование товара"],
            ["name" => "photos", "label" => "Массив фото"],
            ["name" => "video", "label" => "URL видео"],
            ["name" => "characteristics", "label" => "Характеристики"],
            ["name" => "sizes", "label" => "Размеры товара"],
            ["name" => "tags", "label" => "Теги"],
            ["name" => "createdAt", "label" => "Дата создания"],
            ["name" => "updatedAt", "label" => "Дата изменения"],
        ],
        "Габариты упаковки товара, см" => [
            ["name" => "dimensions_length", "label" => "Длина"],
            ["name" => "dimensions_width", "label" => "Ширина"],
            ["name" => "dimensions_height", "label" => "Высота"],
            ["name" => "dimensions_isValid", "label" => "Потенциальная некорректность габаритов товара"],
        ]
    ];

    protected ?int $nmID;
    protected ?int $imtID;
    protected ?string $nmUUID;
    protected ?int $subjectID;
    protected ?string $vendorCode;
    protected ?string $subjectName;
    protected ?string $brand;
    protected ?string $title;
    protected ?Collection $photos;
    protected ?string $video;
    protected ?int $dimensions_length;
    protected ?int $dimensions_width;
    protected ?int $dimensions_height;
    protected ?bool $dimensions_isValid;
    protected ?Collection $characteristics;
    protected ?Collection $sizes;
    protected ?Collection $tags;
    protected ?string $createdAt;
    protected ?string $updatedAt;
    protected ?WbItem $product = null;

    public function __construct(Collection $card = null)
    {
        if ($card) {
            $this->nmID = $card->get('nmID');
            $this->imtID = $card->get('imtID');
            $this->nmUUID = $card->get('nmUUID');
            $this->subjectID = $card->get('subjectID');
            $this->vendorCode = $card->get('vendorCode');
            $this->subjectName = $card->get('subjectName');
            $this->brand = $card->get('brand');
            $this->title = $card->get('title');
            $this->photos = $card->get('photos');
            $this->video = $card->get('video');
            $this->dimensions_length = $card->get('dimensions') ? $card->get('dimensions')->get('length') : $card->get('dimensions_length');
            $this->dimensions_width = $card->get('dimensions') ? $card->get('dimensions')->get('width') : $card->get('dimensions_width');
            $this->dimensions_height = $card->get('dimensions') ? $card->get('dimensions')->get('height') : $card->get('dimensions_height');
            $this->dimensions_isValid = $card->get('dimensions') ? $card->get('dimensions')->get('isValid') : $card->get('dimensions_isValid');
            $this->characteristics = $card->get('characteristics');
            $this->sizes = $card->get('sizes');
            $this->tags = $card->get('tags');
            $this->createdAt = $card->get('createdAt');
            $this->updatedAt = $card->get('updatedAt');
            if ($card->has('product')) {
                $this->product = $card->get('product');
            }
        }
    }

    public function loadLink(WbMarket $market): void
    {
        $this->product = $market->items()->where('nm_id', $this->nmID)->first();
    }

    public function getNmID(): int
    {
        return $this->nmID;
    }

    public function getImtID(): int
    {
        return $this->imtID;
    }

    public function getNmUUID(): string
    {
        return $this->nmUUID;
    }

    public function getSubjectID(): int
    {
        return $this->subjectID;
    }

    public function getVendorCode(): string
    {
        return $this->vendorCode;
    }

    public function getSubjectName(): string
    {
        return $this->subjectName;
    }

    public function getBrand(): string
    {
        return $this->brand;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getPhotos(): Collection
    {
        return $this->photos;
    }

    public function getVideo(): string
    {
        return $this->video;
    }

    public function getDimensionsLength(): int
    {
        return $this->dimensions_length;
    }

    public function getDimensionsWidth(): int
    {
        return $this->dimensions_width;
    }

    public function getDimensionsHeight(): int
    {
        return $this->dimensions_height;
    }

    public function isDimensionsIsValid(): bool
    {
        return $this->dimensions_isValid;
    }

    public function getCharacteristics(): Collection
    {
        return $this->characteristics;
    }

    public function getSizes(): Collection
    {
        return $this->sizes;
    }

    public function getTags(): Collection
    {
        return $this->tags;
    }

    public function getCreatedAt(): string
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): string
    {
        return $this->updatedAt;
    }

    public function getDimensionsIsValid(): ?bool
    {
        return $this->dimensions_isValid;
    }

    public function getProduct(): ?WbItem
    {
        return $this->product;
    }

    public function toLivewire(): array
    {
        return [
            "nmID" => $this->nmID,
            "imtID" => $this->imtID,
            "nmUUID" => $this->nmUUID,
            "subjectID" => $this->subjectID,
            "vendorCode" => $this->vendorCode,
            "subjectName" => $this->subjectName,
            "brand" => $this->brand,
            "title" => $this->title,
            "photos" => $this->photos,
            "video" => $this->video,
            "dimensions_length" => $this->dimensions_length,
            "dimensions_width" => $this->dimensions_width,
            "dimensions_height" => $this->dimensions_height,
            "dimensions_isValid" => $this->dimensions_isValid,
            "characteristics" => $this->characteristics,
            "sizes" => $this->sizes,
            "tags" => $this->tags,
            "createdAt" => $this->createdAt,
            "updatedAt" => $this->updatedAt,
            'product' => $this->product
        ];
    }

    public static function fromLivewire($value): Card
    {
        return new static(collect($value)->toCollectionSpread());
    }
}
