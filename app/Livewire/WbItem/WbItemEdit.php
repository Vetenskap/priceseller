<?php

namespace App\Livewire\WbItem;

use App\Livewire\BaseComponent;
use App\Livewire\Forms\WbItem\WbItemForm;
use App\Models\Bundle;
use App\Models\WbItem;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;

class WbItemEdit extends BaseComponent
{
    public WbItemForm $form;

    public WbItem $item;

    public $searchItems;

    public $items = null;

    public function updatedSearchItems(): void
    {
        $query = $this->form->wbitemable_type === Bundle::class ? auth()->user()->bundles() : auth()->user()->items();

        $this->items = $query
            ->when($this->searchItems, function ($query) {
                $query->where('code', 'like', '%' . $this->searchItems . '%')->orWhere('name', 'like', '%' . $this->searchItems . '%');
            })
            ->limit(15)
            ->get();

        if ($this->form->wbitemable_type === Bundle::class) {
            $item = auth()->user()->bundles()->find($this->item->wbitemable_id);
            if ($item) $this->items->prepend($item);
        } else {
            $item = auth()->user()->items()->find($this->item->wbitemable_id);
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
        $this->form->setWbItem($this->item);
    }
    public function render(): Factory|Application|View|\Illuminate\View\View
    {
        return view('livewire.wb-item.wb-item-edit');
    }
}
