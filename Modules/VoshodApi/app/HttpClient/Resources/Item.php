<?php

namespace Modules\VoshodApi\HttpClient\Resources;

use Illuminate\Support\Collection;

class Item
{
    const ATTRIBUTES = [
        ['name' => 'p_code', 'label' => 'Код поставщика. По-умолчанию, "VNY6"'],
        ['name' => 'mog', 'label' => 'Артикул номенклатуры в базе Восхода'],
        ['name' => 'oem_num', 'label' => 'Артикул производителя'],
        ['name' => 'oem_brand', 'label' => 'Бренд производителя'],
        ['name' => 'name', 'label' => 'Название номенклатуры'],
        ['name' => 'shipment', 'label' => 'Кратность покупки'],
        ['name' => 'delivery', 'label' => 'Срок доставки'],
        ['name' => 'department', 'label' => 'Название отдела в базе Восхода'],
        ['name' => 'unit_code', 'label' => 'Код единицы измерения'],
        ['name' => 'unit', 'label' => 'Единица измерения'],
        ['name' => 'price', 'label' => 'Стоимость товара с учетом количества'],
        ['name' => 'updated_at', 'label' => 'Время последнего обновления товара: цены, остатков'],
        ['name' => 'va_catalog_id', 'label' => 'Идентификатор каталога в 1С'],
        ['name' => 'va_item_id', 'label' => 'Идентификатор товара в 1С'],
    ];

    protected Collection $images;
    protected string $p_code;
    protected string $mog;
    protected string $oem_num;
    protected string $oem_brand;
    protected string $name;
    protected int $shipment;
    protected int $delivery;
    protected string $department;
    protected ?int $count;
    protected ?int $count_chel;
    protected ?int $count_chel_st;
    protected ?int $count_chel_cin;
    protected ?int $count_ekb;
    protected ?int $count_magn;
    protected ?int $count_surgut;
    protected int $unit_code;
    protected string $unit;
    protected float $price;
    protected \DateTime $updated_at;
    protected string $va_catalog_id;
    protected string $va_item_id;

    public function __construct(Collection $product)
    {
        $this->images = $product->get('images', collect());
        $this->p_code = $product->get('p_code');
        $this->mog = $product->get('mog');
        $this->oem_num = $product->get('oem_num');
        $this->oem_brand = $product->get('oem_brand');
        $this->name = $product->get('name');
        $this->shipment = $product->get('shipment');
        $this->delivery = $product->get('delivery');
        $this->department = $product->get('department');
        $this->count = $product->get('count');
        $this->count_chel = $product->get('count_chel');
        $this->count_chel_st = $product->get('count_chel_st');
        $this->count_chel_cin = $product->get('count_chel_cin');
        $this->count_ekb = $product->get('count_ekb');
        $this->count_magn = $product->get('count_magn');
        $this->count_surgut = $product->get('count_surgut');
        $this->unit_code = $product->get('unit_code');
        $this->unit = $product->get('unit');
        $this->price = $product->get('price');
        $this->updated_at = new \DateTime($product->get('updated_at'));
        $this->va_catalog_id = $product->get('va_catalog_id');
        $this->va_item_id = $product->get('va_item_id');
    }

    public function getImages(): array
    {
        return $this->images;
    }

    public function getPCode(): string
    {
        return $this->p_code;
    }

    public function getMog(): string
    {
        return $this->mog;
    }

    public function getOemNum(): string
    {
        return $this->oem_num;
    }

    public function getOemBrand(): string
    {
        return $this->oem_brand;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getShipment(): int
    {
        return $this->shipment;
    }

    public function getDelivery(): int
    {
        return $this->delivery;
    }

    public function getDepartment(): string
    {
        return $this->department;
    }

    public function getCount(): ?int
    {
        return $this->count;
    }

    public function getCountChel(): ?int
    {
        return $this->count_chel;
    }

    public function getCountChelSt(): ?int
    {
        return $this->count_chel_st;
    }

    public function getCountChelCin(): ?int
    {
        return $this->count_chel_cin;
    }

    public function getCountEkb(): ?int
    {
        return $this->count_ekb;
    }

    public function getCountMagn(): ?int
    {
        return $this->count_magn;
    }

    public function getCountSurgut(): ?int
    {
        return $this->count_surgut;
    }

    public function getUnitCode(): int
    {
        return $this->unit_code;
    }

    public function getUnit(): int
    {
        return $this->unit;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function getUpdatedAt(): \DateTime
    {
        return $this->updated_at;
    }

    public function getVaCatalogId(): string
    {
        return $this->va_catalog_id;
    }

    public function getVaItemId(): string
    {
        return $this->va_item_id;
    }


}
