<?php

namespace Modules\Moysklad\Livewire\Forms\MoyskladRetailMarkup;

use Illuminate\Support\Collection;
use Livewire\Attributes\Validate;
use Livewire\Form;
use Modules\Moysklad\Models\Moysklad;

class MoyskladRecountRetailMarkupForm extends Form
{
    public ?Moysklad $moysklad = null;
    public Collection $assortmentAttributes;

    #[Validate]
    public $enabled = false;
    #[Validate]
    public $link;
    #[Validate]
    public $link_name;
    #[Validate]
    public $link_label;
    #[Validate]
    public $link_type;
    #[Validate]
    public $price_type_uuid;

    public function setAssortmentAttributes(Collection $assortmentAttributes): void
    {
        $this->assortmentAttributes = $assortmentAttributes;
    }

    public function setMoysklad(?Moysklad $moysklad): void
    {
        $this->moysklad = $moysklad;
    }

    public function rules(): array
    {
        return [
            'enabled' => ['nullable', 'boolean'],
            'link' => ['string'],
            'link_name' => ['string'],
            'link_label' => ['string'],
            'link_type' => ['string'],
            'price_type_uuid' => ['uuid'],
        ];
    }

    public function setOtherFields(): void
    {
        $assortmentField = $this->assortmentAttributes->where('name', $this->link)->first();
        if (isset($assortmentField['type'])) {
            $this->link_label = $assortmentField['label'];
            $this->link_name= $assortmentField['type'] === 'metadata' ? $assortmentField['label'] : $assortmentField['name'];
            $this->link_type = $assortmentField['type'];
        }
    }


    public function store(): void
    {
        $this->setOtherFields();

        $this->validate();

        $this->moysklad->recountRetailMarkups()->create($this->except(['moysklad', 'assortmentAttributes']));

    }
}
