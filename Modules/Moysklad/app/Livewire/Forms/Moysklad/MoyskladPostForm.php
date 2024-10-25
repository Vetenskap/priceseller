<?php

namespace Modules\Moysklad\Livewire\Forms\Moysklad;

use Illuminate\Support\Collection;
use Livewire\Attributes\Validate;
use Livewire\Form;
use Modules\Moysklad\Models\Moysklad;

class MoyskladPostForm extends Form
{
    public ?Moysklad $moysklad = null;
    public Collection $assortmentAttributes;

    #[Validate]
    public $api_key = null;

    #[Validate]
    public $diff_price = 20;

    #[Validate]
    public $enabled_diff_price = false;

    #[Validate]
    public $enabled_recount_retail_markup = false;

    #[Validate]
    public $link_recount_retail_markup_percent = null;
    #[Validate]
    public $link_name_recount_retail_markup_percent = null;
    #[Validate]
    public $link_label_recount_retail_markup_percent = null;
    #[Validate]
    public $link_type_recount_retail_markup_percent = null;

    public function setAssortmentAttributes(Collection $assortmentAttributes): void
    {
        $this->assortmentAttributes = $assortmentAttributes;
    }

    public function setMoysklad(?Moysklad $moysklad): void
    {
        $this->moysklad = $moysklad;
        if ($moysklad) {
            $this->api_key = $moysklad->api_key;
            $this->diff_price = $moysklad->diff_price;
            $this->enabled_diff_price = $moysklad->enabled_diff_price;
            $this->enabled_recount_retail_markup = (bool) $moysklad->enabled_recount_retail_markup;
            $this->link_recount_retail_markup_percent = $moysklad->link_recount_retail_markup_percent;
            $this->link_name_recount_retail_markup_percent = $moysklad->link_name_recount_retail_markup_percent;
            $this->link_label_recount_retail_markup_percent = $moysklad->link_label_recount_retail_markup_percent;
            $this->link_type_recount_retail_markup_percent = $moysklad->link_type_recount_retail_markup_percent;
        }
    }

    public function rules(): array
    {
        return [
            'api_key' => ['required', 'min:5', 'string'],
            'diff_price' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'enabled_diff_price' => ['nullable', 'boolean'],
            'enabled_recount_retail_markup' => ['nullable', 'boolean'],
            'link_recount_retail_markup_percent' => ['nullable', 'string'],
            'link_name_recount_retail_markup_percent' => ['nullable', 'string'],
            'link_label_recount_retail_markup_percent' => ['nullable', 'string'],
            'link_type_recount_retail_markup_percent' => ['nullable', 'string'],
        ];
    }

    public function setOtherFields(): void
    {
        $assortmentField = $this->assortmentAttributes->where('name', $this->link_recount_retail_markup_percent)->first();
        if (isset($assortmentField['type'])) {
            $this->link_label_recount_retail_markup_percent = $assortmentField['label'];
            $this->link_name_recount_retail_markup_percent = $assortmentField['type'] === 'metadata' ? $assortmentField['label'] : $assortmentField['name'];
            $this->link_type_recount_retail_markup_percent = $assortmentField['type'];
        }
    }


    public function store(): void
    {
        $this->setOtherFields();

        $this->validate();

        if ($this->moysklad) {
            auth()->user()->moysklad()->update($this->except(['moysklad', 'assortmentAttributes']));
        } else {
            $this->moysklad = auth()->user()->moysklad()->create($this->except(['moysklad', 'assortmentAttributes']));
        }

    }
}
