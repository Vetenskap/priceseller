<?php

use Illuminate\Support\Arr;
use Livewire\Attributes\Validate;
use Livewire\Form;
use Modules\Moysklad\Models\Moysklad;

class MoyskladPostForm extends Form
{
    public ?Moysklad $moysklad = null;

    #[Validate]
    public $api_key = null;

    public function setMoysklad(?Moysklad $moysklad): void
    {
        $this->moysklad = $moysklad;
        if ($moysklad) {
            $this->api_key = $moysklad->api_key;
        }
    }

    public function rules(): array
    {
        return [
            'api_key' => ['required', 'min:5', 'string'],
        ];
    }


    public function store(): void
    {
        $this->validate();

        if ($this->moysklad) {
            auth()->user()->moysklad()->update($this->except('moysklad'));
        } else {
            $this->moysklad = auth()->user()->moysklad()->create($this->except('moysklad'));
        }

    }
}
