<?php

namespace App\Livewire\OzonItem;

use App\Livewire\BaseComponent;
use App\Livewire\Forms\OzonItem\OzonItemForm;
use App\Models\Bundle;
use App\Models\OzonItem;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;

class OzonItemEdit extends BaseComponent
{

    public OzonItemForm $form;

    public OzonItem $item;

    public $searchItems;

    public $items = null;

    public function updatedSearchItems(): void
    {
        $query = $this->form->ozonitemable_type === Bundle::class ? auth()->user()->bundles() : auth()->user()->items();

        $this->items = $query
            ->when($this->searchItems, function ($query) {
                $query->where('code', 'like', '%' . $this->searchItems . '%')->orWhere('name', 'like', '%' . $this->searchItems . '%');
            })
            ->limit(15)
            ->get();

        if ($this->form->ozonitemable_type === Bundle::class) {
            $item = auth()->user()->bundles()->find($this->item->ozonitemable_id);
            if ($item) $this->items->prepend($item);
        } else {
            $item = auth()->user()->items()->find($this->item->ozonitemable_id);
            if ($item) $this->items->prepend($item);
        }

    }

    public function update(): void
    {
        // TODO: add auth

        $this->form->update();

        $this->addSuccessSaveNotification();
    }

    public function destroy(): void
    {
        // TODO: add auth

        $this->form->destroy();

    }

    public function mount(): void
    {
        $this->form->setOzonItem($this->item);
    }

    public function render(): Factory|Application|View|\Illuminate\View\View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.ozon-item.ozon-item-edit');
    }
}
