<?php

namespace App\Livewire\Moysklad;

use App\Models\Moysklad;
use Livewire\Component;

class MoyskladIndex extends Component
{
    public Moysklad $moysklad;

    public $api_key;

    public $name;

    public function mount()
    {
        $this->moysklad = Moysklad::where('user_id', auth()->user()->id)->first();
        $this->api_key = $this->moysklad->api_key;
        $this->name = $this->moysklad->name;
    }

    public function render()
    {
        return view('livewire.moysklad.moysklad-index');
    }
}
