<?php

namespace Modules\Moysklad\HttpClient\Resources\Entities\Bundle;

use DateTime;
use Illuminate\Support\Collection;
use Modules\Moysklad\HttpClient\Resources\Entities\Bundle\MetaArrays\Component;
use Modules\Moysklad\HttpClient\Resources\Entities\Entity;
use Modules\Moysklad\HttpClient\Resources\Entities\Product\Metadata\Attribute;

class Bundle extends Entity
{
    const FIELDS = [
        ['name' => 'accountId', 'label' => 'ID учетной записи', 'type' => 'main'],
        ['name' => 'archived', 'label' => 'Добавлен ли Комплект в архив', 'type' => 'main'],
        ['name' => 'article', 'label' => 'Артикул', 'type' => 'main'],
        ['name' => 'code', 'label' => 'Код Комплекта', 'type' => 'main'],
        ['name' => 'description', 'label' => 'Описание Комплекта', 'type' => 'main'],
        ['name' => 'discountProhibited', 'label' => 'Признак запрета скидок', 'type' => 'main'],
        ['name' => 'effectiveVat', 'label' => 'Реальный НДС %'],
        ['name' => 'effectiveVatEnabled', 'label' => 'Дополнительный признак для определения разграничения реального НДС', 'type' => 'main'],
        ['name' => 'externalCode', 'label' => 'Внешний код Комплекта', 'type' => 'main'],
        ['name' => 'id', 'label' => 'ID Комплекта', 'type' => 'main'],
        ['name' => 'name', 'label' => 'Наименование Комплекта', 'type' => 'main'],
        ['name' => 'partialDisposal', 'label' => 'Управление состоянием частичного выбытия маркированного товара', 'type' => 'main'],
        ['name' => 'pathName', 'label' => 'Наименование группы, в которую входит Комплект', 'type' => 'main'],
        ['name' => 'shared', 'label' => 'Общий доступ', 'type' => 'main'],
        ['name' => 'syncId', 'label' => 'ID синхронизации', 'type' => 'main'],
        ['name' => 'tnved', 'label' => 'Код ТН ВЭД', 'type' => 'main'],
        ['name' => 'updated', 'label' => 'Момент последнего обновления сущности', 'type' => 'main'],
        ['name' => 'useParentVat', 'label' => 'Используется ли ставка НДС родительской группы', 'type' => 'main'],
        ['name' => 'vat', 'label' => 'НДС %', 'type' => 'main'],
        ['name' => 'vatEnabled', 'label' => 'Включен ли НДС для товара', 'type' => 'main'],
        ['name' => 'volume', 'label' => 'Объем', 'type' => 'main'],
        ['name' => 'weight', 'label' => 'Вес', 'type' => 'main'],
    ];

    const ENDPOINT = '/entity/bundle/';

    protected string $accountId;
    protected bool $archived;
    protected ?string $article = null;
    protected ?string $code = null;
    protected ?string $description = null;
    protected bool $discountProhibited;
    protected ?int $effectiveVat = null;
    protected ?bool $effectiveVatEnabled = null;
    protected string $externalCode;
    protected string $name;
    protected ?bool $partialDisposal = null;
    protected string $pathName;
    protected bool $shared;
    protected ?string $syncId = null;
    protected ?string $tnved = null;
    protected DateTime $updated;
    protected bool $useParentVat;
    protected ?int $vat = null;
    protected ?bool $vatEnabled = null;
    protected ?int $volume = null;
    protected ?int $weight = null;

    protected ?Collection $attributes = null;

    protected ?Collection $components = null;

    public function __construct(?Collection $bundle = null)
    {
        if ($bundle) {

            $this->set($bundle);

        }
    }

    protected function set(Collection $bundle): void
    {
        $this->data = $bundle;
        $this->id = $bundle->get('id');
        $this->accountId = $bundle->get('accountId');
        $this->archived = $bundle->get('archived');
        $this->article = $bundle->get('article');
        $this->code = $bundle->get('code');
        $this->description = $bundle->get('description');
        $this->discountProhibited = $bundle->get('discountProhibited');
        $this->effectiveVat = $bundle->get('effectiveVat');
        $this->effectiveVatEnabled = $bundle->get('effectiveVatEnabled');
        $this->externalCode = $bundle->get('externalCode');
        $this->name = $bundle->get('name');
        $this->partialDisposal = $bundle->get('partialDisposal');
        $this->pathName = $bundle->get('pathName');
        $this->shared = $bundle->get('shared');
        $this->syncId = $bundle->get('syncId');
        $this->tnved = $bundle->get('tnved');
        $this->updated = new DateTime($bundle->get('updated'));
        $this->useParentVat = $bundle->get('useParentVat');
        $this->vat = $bundle->get('vat');
        $this->vatEnabled = $bundle->get('vatEnabled');
        $this->volume = $bundle->get('volume');
        $this->weight = $bundle->get('weight');

        if ($bundle->has('attributes')) {

            $this->attributes = new Collection();

            foreach ($bundle->get('attributes') as $attribute) {
                $this->attributes->push(new Attribute(collect($attribute)));
            }
        }

        if ($bundle->has('components')) {

            $this->components = collect();

            $components = collect($bundle->get('components'));

            if ($components->has('rows')) {

                foreach ($components->get('rows') as $component) {

                    $this->components->push(new Component(collect($component)));
                }
            }
        }
    }

    public function getAccountId(): string
    {
        return $this->accountId;
    }

    public function isArchived(): bool
    {
        return $this->archived;
    }

    public function getArticle(): string
    {
        return $this->article;
    }

    public function getCode(): string
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

    public function isEffectiveVatEnabled(): bool
    {
        return $this->effectiveVatEnabled;
    }

    public function getExternalCode(): string
    {
        return $this->externalCode;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function isPartialDisposal(): bool
    {
        return $this->partialDisposal;
    }

    public function getPathName(): ?string
    {
        return $this->pathName;
    }

    public function isShared(): bool
    {
        return $this->shared;
    }

    public function getSyncId(): ?string
    {
        return $this->syncId;
    }

    public function getTnved(): ?string
    {
        return $this->tnved;
    }

    public function getUpdated(): DateTime
    {
        return $this->updated;
    }

    public function isUseParentVat(): bool
    {
        return $this->useParentVat;
    }

    public function getVat(): int
    {
        return $this->vat;
    }

    public function isVatEnabled(): bool
    {
        return $this->vatEnabled;
    }

    public function getVolume(): int
    {
        return $this->volume;
    }

    public function getWeight(): int
    {
        return $this->weight;
    }

    public function getAttributes(): ?Collection
    {
        return $this->attributes;
    }

    public function getComponents(): ?Collection
    {
        return $this->components;
    }

    public function toArray(): array
    {
        return [
            'accountId' => $this->accountId,
            'archived' => $this->archived,
            'article' => $this->article,
            'code' => $this->code,
            'description' => $this->description,
            'discountProhibited' => $this->discountProhibited,
            'effectiveVat' => $this->effectiveVat,
            'effectiveVatEnabled' => $this->effectiveVatEnabled,
            'externalCode' => $this->externalCode,
            'name' => $this->name,
            'partialDisposal' => $this->partialDisposal,
            'pathName' => $this->pathName,
            'shared' => $this->shared,
            'syncId' => $this->syncId,
            'tnved' => $this->tnved,
            'updated' => $this->updated->format('Y-m-d H:i:s'), // Преобразование DateTime в строку
            'useParentVat' => $this->useParentVat,
            'vat' => $this->vat,
            'vatEnabled' => $this->vatEnabled,
            'volume' => $this->volume,
            'weight' => $this->weight,

            // Преобразование коллекций в массивы
            'attributes' => $this->attributes?->map(fn (Attribute $attribute) => $attribute->toArray())->toArray(),
            'components' => $this->components?->map(fn (Component $component) => $component->toArray())->toArray(),
        ];
    }
}
