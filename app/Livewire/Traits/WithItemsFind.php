<?php

namespace App\Livewire\Traits;

trait WithItemsFind
{
    public $searchItems;

    public $items = null;

    public function updatedSearchItems(): void
    {
        $this->items = $this->currentUser()
            ->items()
            ->when($this->searchItems, function ($query) {
                $query->where('code', 'like', '%' . $this->searchItems . '%')->orWhere('name', 'like', '%' . $this->searchItems . '%');
            })
            ->limit(15)
            ->get();

    }
}
