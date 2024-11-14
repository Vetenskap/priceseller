<?php

namespace Modules\Moysklad\Livewire\Forms\Moysklad;

use Illuminate\Support\Collection;
use Livewire\Attributes\Validate;
use Livewire\Form;
use Modules\Moysklad\Models\Moysklad;

class MoyskladPostForm extends Form
{
    public ?Moysklad $moysklad = null;
    public ?Collection $assortmentAttributes = null;

    #[Validate]
    public $api_key = null;

    #[Validate]
    public $diff_price = 20;

    #[Validate]
    public $enabled_diff_price = false;

    public function setMoysklad(?Moysklad $moysklad): void
    {
        $this->moysklad = $moysklad;
        if ($moysklad) {
            $this->api_key = $moysklad->api_key;
            $this->diff_price = $moysklad->diff_price;
            $this->enabled_diff_price = $moysklad->enabled_diff_price;
        }
    }

    public function rules(): array
    {
        return [
            'api_key' => ['required', 'min:5', 'string'],
            'diff_price' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'enabled_diff_price' => ['nullable', 'boolean'],
        ];
    }

    public function store(): void
    {
        $this->validate();

        if ($this->moysklad) {
            auth()->user()->moysklad()->update($this->except(['moysklad', 'assortmentAttributes']));
        } else {
            $this->moysklad = auth()->user()->moysklad()->create($this->except(['moysklad', 'assortmentAttributes']));
        }

    }
}
