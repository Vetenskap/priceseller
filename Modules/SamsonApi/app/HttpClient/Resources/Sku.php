<?php

namespace Modules\SamsonApi\HttpClient\Resources;

use Illuminate\Support\Collection;

class Sku
{
    const ENDPOINT = '/sku/';
    const ATTRIBUTES = [
        ['name' => 'sku', 'label' => 'Код'],
        ['name' => 'name', 'label' => 'Наименование продукта'],
        ['name' => 'name_1c', 'label' => 'Наименование товара в 1С'],
        ['name' => 'manufacturer', 'label' => 'Производитель'],
        ['name' => 'vendor_code', 'label' => 'Артикул'],
        ['name' => 'barcode', 'label' => 'Штрих-код'],
        ['name' => 'brand', 'label' => 'Бренд'],
        ['name' => 'description', 'label' => 'Описание'],
        ['name' => 'description_ext', 'label' => 'Расширенное описание'],
        ['name' => 'weight', 'label' => 'Вес'],
        ['name' => 'volume', 'label' => 'Объём'],
        ['name' => 'nds', 'label' => 'НДС'],
        ['name' => 'ban_not_multiple', 'label' => 'Запрет некратного набора'],
        ['name' => 'out_of_stock', 'label' => 'Вывод из ассортимента'],
        ['name' => 'remove_date', 'label' => 'Дата распродажи'],
        ['name' => 'expiration_date', 'label' => 'Срок годности'],
        ['name' => 'manufacturer_code', 'label' => 'Код страны производителя'],
        ['name' => 'sale_date', 'label' => 'Дата распродажи'],
        ['name' => 'infiltration', 'label' => 'Рекомендованная розничная цена'],
        ['name' => 'contract', 'label' => 'Договорная цена'],
        ['name' => 'idp', 'label' => 'Остаток в филиале'],
        ['name' => 'transit', 'label' => 'Количество в пути'],
        ['name' => 'distribution_warehouse', 'label' => 'Остатки на РЦ'],
        ['name' => 'total', 'label' => 'Cумма остатков'],
        ['name' => 'min_opt', 'label' => 'Минимальная партия ОПТ'],
        ['name' => 'min_kor', 'label' => 'Минимальная партия КОР'],
        ['name' => 'pzk', 'label' => 'Партия под заказ'],
        ['name' => 'intermediate', 'label' => 'Промежуточная упаковка'],
        ['name' => 'transport', 'label' => 'Транспортная упаковка'],
        ['name' => 'unit', 'label' => 'Размерность мин. партии'],
        ['name' => 'height', 'label' => 'Высоат'],
        ['name' => 'width', 'label' => 'Ширина'],
        ['name' => 'depth', 'label' => 'Глубина'],
        ['name' => 'novelty', 'label' => 'Новинка'],
    ];

    protected int $sku; // Код (6 символов)
    protected string $name; // Наименование продукта
    protected ?string $name_1c; // Наименование товара в 1С
    protected ?string $manufacturer; // Производитель
    protected ?string $vendor_code; // Артикул
    protected ?string $barcode; // Штрих-код
    protected ?string $brand; // Бренд
    protected ?string $description; // Описание
    protected ?string $description_ext; // Расширенное описание
    protected ?float $weight; // Вес
    protected ?float $volume; // Объём
    protected ?float $nds; // НДС
    protected ?int $ban_not_multiple; // Запрет некратного набора (1 - да, 0 - нет)
    protected ?int $out_of_stock; // Вывод из ассортимента (1 - да, 0 - нет)
    protected ?\DateTime $remove_date; // Дата распродажи
    protected ?float $expiration_date; // Срок годности
    protected ?int $manufacturer_code; // Код страны производителя
    protected ?\DateTime $sale_date; // Дата распродажи
    protected ?float $infiltration;
    protected ?float $contract;
    protected ?int $idp;
    protected ?int $transit;
    protected ?int $distribution_warehouse;
    protected ?int $total;
    protected ?int $min_opt; // минимальная партия ОПТ
    protected ?int $min_kor; // минимальная партия КОР
    protected ?int $pzk; // партия под заказ
    protected ?int $intermediate; // промежуточная упаковка
    protected ?int $transport; // транспортная упаковка
    protected int|string|null $unit; // размерность мин. партии
    protected ?int $height; // высота
    protected ?int $width; // ширина
    protected ?int $depth; // глубина
    protected ?int $novelty;

    public function __construct(Collection $sku)
    {
        $this->sku = $sku->get('sku');
        $this->name = $sku->get('name');
        $this->name_1c = $sku->get('name_1c');
        $this->manufacturer = $sku->get('manufacturer');
        $this->vendor_code = $sku->get('vendor_code');
        $this->barcode = $sku->get('barcode');
        $this->brand = $sku->get('brand');
        $this->description = $sku->get('description');
        $this->description_ext = $sku->get('description_ext');
        $this->weight = $sku->get('weight');
        $this->volume = $sku->get('volume');
        $this->nds = $sku->get('nds');
        $this->ban_not_multiple = $sku->get('ban_not_multiple');
        $this->out_of_stock = $sku->get('out_of_stock');
        $this->remove_date = $sku->has('remove_date') ? new \DateTime($sku->get('remove_date')) : null;
        $this->expiration_date = $sku->get('expiration_date');
        $this->manufacturer_code = $sku->get('manufacturer_code');
        $this->sale_date = $sku->has('sale_date') ? new \DateTime($sku->get('sale_date')) : null;

        $this->infiltration = $sku->getFromDotWithValue('price_list.type', 'infiltration')?->get('value');
        $this->contract = $sku->getFromDotWithValue('price_list.type', 'contract')?->get('value');

        $this->idp = $sku->getFromDotWithValue('stock_list.type', 'idp')?->get('value');
        $this->transit = $sku->getFromDotWithValue('stock_list.type', 'transit')?->get('value');
        $this->distribution_warehouse = $sku->getFromDotWithValue('stock_list.type', 'distribution_warehouse')?->get('value');
        $this->total = $sku->getFromDotWithValue('stock_list.type', 'total')?->get('value');

        $this->min_opt = $sku->getFromDotWithValue('package_list.type', 'min_opt')?->get('value');
        $this->min_kor = $sku->getFromDotWithValue('package_list.type', 'min_kor')?->get('value');
        $this->pzk = $sku->getFromDotWithValue('package_list.type', 'pzk')?->get('value');
        $this->intermediate = $sku->getFromDotWithValue('package_list.type', 'intermediate')?->get('value');
        $this->transport = $sku->getFromDotWithValue('package_list.type', 'transport')?->get('value');
        $this->unit = $sku->getFromDotWithValue('package_list.type', 'unit')?->get('value');

        $this->height = $sku->getFromDotWithValue('package_size.type', 'height')?->get('value');
        $this->width = $sku->getFromDotWithValue('package_size.type', 'width')?->get('value');
        $this->depth = $sku->getFromDotWithValue('package_size.type', 'depth')?->get('value');

        $this->novelty = $sku->getFromDotWithValue('attribute_list.type', 'novelty')?->get('value');
    }

    public function getSku(): int
    {
        return $this->sku;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getName1c(): ?string
    {
        return $this->name_1c;
    }

    public function getManufacturer(): ?string
    {
        return $this->manufacturer;
    }

    public function getVendorCode(): ?string
    {
        return $this->vendor_code;
    }

    public function getBarcode(): ?string
    {
        return $this->barcode;
    }

    public function getBrand(): ?string
    {
        return $this->brand;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getDescriptionExt(): ?string
    {
        return $this->description_ext;
    }

    public function getWeight(): ?float
    {
        return $this->weight;
    }

    public function getVolume(): ?float
    {
        return $this->volume;
    }

    public function getNds(): ?float
    {
        return $this->nds;
    }

    public function getBanNotMultiple(): ?int
    {
        return $this->ban_not_multiple;
    }

    public function getOutOfStock(): ?int
    {
        return $this->out_of_stock;
    }

    public function getRemoveDate(): ?\DateTime
    {
        return $this->remove_date;
    }

    public function getExpirationDate(): ?float
    {
        return $this->expiration_date;
    }

    public function getManufacturerCode(): ?int
    {
        return $this->manufacturer_code;
    }

    public function getSaleDate(): ?\DateTime
    {
        return $this->sale_date;
    }

    public function getInfiltration(): ?float
    {
        return $this->infiltration;
    }

    public function getContract(): ?float
    {
        return $this->contract;
    }

    public function getIdp(): ?int
    {
        return $this->idp;
    }

    public function getTransit(): ?int
    {
        return $this->transit;
    }

    public function getDistributionWarehouse(): ?int
    {
        return $this->distribution_warehouse;
    }

    public function getTotal(): ?int
    {
        return $this->total;
    }

    public function getMinOpt(): ?int
    {
        return $this->min_opt;
    }

    public function getMinKor(): ?int
    {
        return $this->min_kor;
    }

    public function getPzk(): ?int
    {
        return $this->pzk;
    }

    public function getIntermediate(): ?int
    {
        return $this->intermediate;
    }

    public function getTransport(): ?int
    {
        return $this->transport;
    }

    public function getUnit(): int|string|null
    {
        return $this->unit;
    }

    public function getHeight(): ?int
    {
        return $this->height;
    }

    public function getWidth(): ?int
    {
        return $this->width;
    }

    public function getDepth(): ?int
    {
        return $this->depth;
    }

    public function getNovelty(): ?int
    {
        return $this->novelty;
    }



}
