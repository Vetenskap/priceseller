<?php

namespace App\Livewire\Forms;

use App\Models\Moysklad;
use Livewire\Attributes\Validate;
use Livewire\Form;

class MoyskladPostForm extends Form
{
    public ?Moysklad $moysklad;

    public $api_key;

    public $name;

    public function setMoysklad(?Moysklad $moysklad)
    {
        $this->moysklad = $moysklad;
        $this->api_key = $moysklad?->api_key;
        $this->name = $moysklad?->name;
    }

    public function store()
    {
        auth()->user()->moysklad()->create($this->except('moysklad'));
    }

    public function update()
    {
        $this->moysklad->update($this->except('moysklad'));
    }
}
