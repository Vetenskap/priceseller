<?php

namespace App\Livewire\OzonItem;

use App\Livewire\BaseComponent;
use App\Models\OzonItem;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;

class OzonItemEdit extends BaseComponent
{
    public OzonItem $item;

    public function render(): Factory|Application|View|\Illuminate\View\View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.ozon-item.ozon-item-edit');
    }
}
