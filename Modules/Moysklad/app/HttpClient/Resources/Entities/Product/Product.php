<?php

namespace Modules\Moysklad\HttpClient\Resources\Entities\Product;

use Illuminate\Support\Collection;
use Modules\Moysklad\HttpClient\MoyskladClient;
use Modules\Moysklad\HttpClient\Resources\Entities\Counterparty;
use Modules\Moysklad\HttpClient\Resources\Entities\Employee;
use Modules\Moysklad\HttpClient\Resources\Entities\Entity;
use Modules\Moysklad\HttpClient\Resources\Entities\Group;
use Modules\Moysklad\HttpClient\Resources\Entities\Product\Arrays\Pack;
use Modules\Moysklad\HttpClient\Resources\Entities\Product\Metadata\Attribute;
use Modules\Moysklad\HttpClient\Resources\Entities\ProductFolder;
use Modules\Moysklad\HttpClient\Resources\Entities\Uom;
use Modules\Moysklad\HttpClient\Resources\Objects\Alcoholic;
use Modules\Moysklad\HttpClient\Resources\Objects\BuyPrice;
use Modules\Moysklad\HttpClient\Resources\Objects\MinPrice;
use Modules\Moysklad\Models\Moysklad;

class Product extends Entity
{
    const FIELDS = [
        ['name' => 'accountId', 'label' => 'ID учетной записи', 'type' => 'main'],
        ['name' => 'archived', 'label' => 'Добавлен ли Товар в архив', 'type' => 'main'],
        ['name' => 'article', 'label' => 'Артикул', 'type' => 'main'],
        ['name' => 'code', 'label' => 'Код Товара', 'type' => 'main'],
        ['name' => 'description', 'label' => 'Описание Товара', 'type' => 'main'],
        ['name' => 'discountProhibited', 'label' => 'Признак запрета скидок', 'type' => 'main'],
        ['name' => 'effectiveVat', 'label' => 'Реальный НДС %', 'type' => 'main'],
        ['name' => 'effectiveVatEnabled', 'label' => 'Дополнительный признак для определения разграничения реального НДС', 'type' => 'main'],
        ['name' => 'externalCode', 'label' => 'Внешний код Товара', 'type' => 'main'],
        ['name' => 'isSerialTrackable', 'label' => 'Учет по серийным номерам', 'type' => 'main'],
        ['name' => 'minimumBalance', 'label' => 'Неснижаемый остаток', 'type' => 'main'],
        ['name' => 'name', 'label' => 'Наименование Товара', 'type' => 'main'],
        ['name' => 'partialDisposal', 'label' => 'Управление состоянием частичного выбытия маркированного товара', 'type' => 'main'],
        ['name' => 'pathName', 'label' => 'Наименование группы, в которую входит Товар', 'type' => 'main'],
        ['name' => 'paymentItemType', 'label' => 'Признак предмета расчета', 'type' => 'main'],
        ['name' => 'ppeType', 'label' => 'Код вида номенклатурной классификации медицинских средств индивидуальной защиты', 'type' => 'main'],
        ['name' => 'shared', 'label' => 'Общий доступ', 'type' => 'main'],
        ['name' => 'syncId', 'label' => 'ID синхронизации', 'type' => 'main'],
        ['name' => 'taxSystem', 'label' => 'Код системы налогообложения', 'type' => 'main'],
        ['name' => 'tnved', 'label' => 'Код ТН ВЭД', 'type' => 'main'],
        ['name' => 'trackingType', 'label' => 'Тип маркируемой продукции', 'type' => 'main'],
        ['name' => 'updated', 'label' => 'Момент последнего обновления сущности', 'type' => 'main'],
        ['name' => 'useParentVat', 'label' => 'Используется ли ставка НДС родительской группы', 'type' => 'main'],
        ['name' => 'variantsCount', 'label' => 'Количество модификаций у данного товара', 'type' => 'main'],
        ['name' => 'vat', 'label' => 'НДС %', 'type' => 'main'],
        ['name' => 'vatEnabled', 'label' => 'Включен ли НДС для товара', 'type' => 'main'],
        ['name' => 'volume', 'label' => 'Объем', 'type' => 'main'],
        ['name' => 'weight', 'label' => 'Вес', 'type' => 'main'],
        ['name' => 'buyPrice', 'label' => 'Закупочная цена', 'type' => 'object.value'],
        ['name' => 'minPrice', 'label' => 'Минимальная цена', 'type' => 'object.value'],
    ];

    const ENDPOINT = '/entity/product/';

    protected string $accountId;
    protected bool $archived;
    protected ?string $article = null;
    protected ?string $code = null;
    protected ?string $description = null;
    protected bool $discountProhibited;
    protected ?int $effectiveVat = null;
    protected ?bool $effectiveVatEnabled = null;
    protected string $externalCode;
    protected ?bool $isSerialTrackable = null;
    protected ?int $minimumBalance = null;
    protected string $name;
    protected ?bool $partialDisposal = null;
    protected string $pathName;

    // TODO: мой склад enum
    protected ?string $paymentItemType = null;
    // TODO: мой склад enum
    protected ?string $ppeType = null;
    protected bool $shared;
    protected ?string $syncId = null;
    // TODO: мой склад enum
    protected ?string $taxSystem = null;
    protected ?string $tnved = null;
    // TODO: мой склад enum
    protected ?string $trackingType = null;
    protected string $updated;
    protected bool $useParentVat;
    protected int $variantsCount;
    protected ?int $vat = null;
    protected ?bool $vatEnabled = null;
    protected ?int $volume = null;
    protected ?int $weight = null;

    // Expand
    protected Group $group;

    // Expand
    protected ?Employee $owner = null;

    // Expand
    protected ?ProductFolder $productFolder = null;

    // Expand
    protected ?Counterparty $supplier = null;

    // Expand
    protected ?Uom $uom = null;

    protected ?Alcoholic $alcoholic = null;
    protected ?BuyPrice $buyPrice = null;
    protected ?MinPrice $minPrice = null;
    protected ?Collection $attributes = null;
    protected ?Collection $barcodes = null;
    protected ?Collection $packs = null;

    public function __construct(?Collection $product = null)
    {
        if ($product) {

            $this->set($product);

        } else {
            $this->buyPrice = new BuyPrice();
        }
    }

    protected function set(Collection $product): void
    {
        $this->data = $product;
        $this->accountId = $product->get('accountId');
        $this->archived = $product->get('archived');
        $this->article = $product->get('article');
        $this->code = $product->get('code');
        $this->description = $product->get('description');
        $this->discountProhibited = $product->get('discountProhibited');
        $this->effectiveVat = $product->get('effectiveVat');
        $this->effectiveVatEnabled = $product->get('effectiveVatEnabled');
        $this->externalCode = $product->get('externalCode');
        $this->id = $product->get('id');
        $this->isSerialTrackable = $product->get('isSerialTrackable');
        $this->minimumBalance = $product->get('minimumBalance');
        $this->name = $product->get('name');
        $this->partialDisposal = $product->get('partialDisposal');
        $this->pathName = $product->get('pathName');
        $this->paymentItemType = $product->get('paymentItemType');
        $this->ppeType = $product->get('ppeType');
        $this->shared = $product->get('shared');
        $this->syncId = $product->get('syncId');
        $this->taxSystem = $product->get('taxSystem');
        $this->tnved = $product->get('tnved');
        $this->trackingType = $product->get('trackingType');
        $this->updated = $product->get('updated');
        $this->useParentVat = $product->get('useParentVat');
        $this->variantsCount = $product->get('variantsCount');
        $this->vat = $product->get('vat');
        $this->vatEnabled = $product->get('vatEnabled');
        $this->volume = $product->get('volume');
        $this->weight = $product->get('weight');

        $group = new Group();
        $group->setId(collect($product->get('group'))->toCollectionSpread()->get('meta')->get('href'));
        $this->group = $group;

        if ($product->has('uom')) {
            $uom = new Uom();
            $uom->setId(collect($product->get('uom'))->toCollectionSpread()->get('meta')->get('href'));
            $this->uom = $uom;
        }

        if ($product->has('owner')) {
            $owner = new Employee();
            $owner->setId(collect($product->get('owner'))->toCollectionSpread()->get('meta')->get('href'));
            $this->owner = $owner;
        }

        if ($product->has('productFolder')) {
            $productFolder = new ProductFolder();
            $productFolder->setId(collect($product->get('productFolder'))->toCollectionSpread()->get('meta')->get('href'));
            $this->productFolder = $productFolder;
        }

        if ($product->has('supplier')) {
            $counterparty = new Counterparty();
            $counterparty->setId(collect($product->get('supplier'))->toCollectionSpread()->get('meta')->get('href'));
            $this->supplier = $counterparty;
        }

        if ($product->has('alcoholic')) {
            $this->alcoholic = new Alcoholic(collect($product->get('alcoholic')));
        }

        if ($product->has('attributes')) {

            $this->attributes = new Collection();

            foreach ($product->get('attributes') as $attribute) {
                $this->attributes->push(new Attribute(collect($attribute)));
            }
        }

        if ($product->has('barcodes')) {
            $this->barcodes = collect($product->get('barcodes'));
        }

        if ($product->has('minPrice')) {
            $this->minPrice = new MinPrice(collect($product->get('minPrice')));
        }

        if ($product->has('buyPrice')) {
            $this->buyPrice = new BuyPrice(collect($product->get('buyPrice')));
        }

        if ($product->has('packs')) {

            $this->packs = new Collection();

            foreach ($product->get('packs') as $pack) {
                $this->attributes->push(new Pack(collect($pack)));
            }

        }

        // TODO: Мой склад
        if ($product->has('country')) {

        }

        // TODO: Мой склад
        if ($product->has('files')) {

        }

        // TODO: Мой склад
        if ($product->has('images')) {

        }

        // TODO: Мой склад
        if ($product->has('salePrices')) {

        }

        // TODO: Мой склад
        if ($product->has('things')) {

        }
    }

    public function update(Moysklad $moysklad, array $fields = []): bool
    {
        if (in_array('buyPrice', $fields)) {
            $data = $this->buyPrice->getFieldProduct();
            return $this->put($moysklad->api_key, $data);
        }

        return false;
    }

    public function getField(): array
    {
        return [
            "meta" => [
                "href" => "https://api.moysklad.ru/api/remap/1.2/entity/product/{$this->id}",
                "metadataHref" => "https://api.moysklad.ru/api/remap/1.2/entity/product/metadata",
                "type" => "product",
                "mediaType" => "application/json"
            ],
        ];
    }

    public static function updateMassive(Moysklad $moysklad, array $data): Collection
    {
        return static::post($moysklad->api_key, $data);

    }

    public function arrayToMassive(array $fields = []): array
    {
        if (in_array('buyPrice', $fields)) {
            return array_merge($this->getField(), $this->buyPrice->getFieldProduct());
        }

        return [];
    }

    public function getAccountId(): string
    {
        return $this->accountId;
    }

    public function isArchived(): bool
    {
        return $this->archived;
    }

    public function getArticle(): ?string
    {
        return $this->article;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function isDiscountProhibited(): bool
    {
        return $this->discountProhibited;
    }

    public function getEffectiveVat(): ?int
    {
        return $this->effectiveVat;
    }

    public function getEffectiveVatEnabled(): ?bool
    {
        return $this->effectiveVatEnabled;
    }

    public function getExternalCode(): string
    {
        return $this->externalCode;
    }

    public function getIsSerialTrackable(): ?bool
    {
        return $this->isSerialTrackable;
    }

    public function getMinimumBalance(): ?int
    {
        return $this->minimumBalance;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPartialDisposal(): ?bool
    {
        return $this->partialDisposal;
    }

    public function getPathName(): string
    {
        return $this->pathName;
    }

    public function getPaymentItemType(): ?string
    {
        return $this->paymentItemType;
    }

    public function getPpeType(): ?string
    {
        return $this->ppeType;
    }

    public function isShared(): bool
    {
        return $this->shared;
    }

    public function getSyncId(): ?string
    {
        return $this->syncId;
    }

    public function getTaxSystem(): ?string
    {
        return $this->taxSystem;
    }

    public function getTnved(): ?string
    {
        return $this->tnved;
    }

    public function getTrackingType(): ?string
    {
        return $this->trackingType;
    }

    public function getUpdated(): string
    {
        return $this->updated;
    }

    public function isUseParentVat(): bool
    {
        return $this->useParentVat;
    }

    public function getVariantsCount(): int
    {
        return $this->variantsCount;
    }

    public function getVat(): ?int
    {
        return $this->vat;
    }

    public function getVatEnabled(): ?bool
    {
        return $this->vatEnabled;
    }

    public function getVolume(): ?int
    {
        return $this->volume;
    }

    public function getWeight(): ?int
    {
        return $this->weight;
    }

    public function getGroup(): Group
    {
        return $this->group;
    }

    public function getOwner(): ?Employee
    {
        return $this->owner;
    }

    public function getProductFolder(): ?ProductFolder
    {
        return $this->productFolder;
    }

    public function getSupplier(): ?Counterparty
    {
        return $this->supplier;
    }

    public function getUom(): ?Uom
    {
        return $this->uom;
    }

    public function getAlcoholic(): ?Alcoholic
    {
        return $this->alcoholic;
    }

    public function getBuyPrice(): ?BuyPrice
    {
        return $this->buyPrice;
    }

    public function getMinPrice(): ?MinPrice
    {
        return $this->minPrice;
    }

    public function getAttributes(): ?Collection
    {
        return $this->attributes;
    }

    public function getBarcodes(): ?Collection
    {
        return $this->barcodes;
    }

    public function getPacks(): ?Collection
    {
        return $this->packs;
    }


}
