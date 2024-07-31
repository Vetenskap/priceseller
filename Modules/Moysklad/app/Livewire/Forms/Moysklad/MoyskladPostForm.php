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

    public function setMoysklad(?Moysklad $moysklad)
    {
        $this->moysklad = $moysklad;
        if ($moysklad) {
            $this->api_key = $moysklad->api_key;
        }
    }

    public function rules()
    {
        return [
            'api_key' => ['required', 'min:5', 'string'],
        ];
    }


    public function store()
    {
        $this->validate();

        $moysklad = Moysklad::create(Arr::add($this->except('moysklad'), 'user_id', \auth()->user()->id));
        $moysklad->refresh();

        return $moysklad;

    }

    public function update()
    {
        $this->validate();

        $this->moysklad->update($this->except('moysklad'));

    }
}
