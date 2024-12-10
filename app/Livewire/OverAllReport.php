<?php

namespace App\Livewire;

use App\Models\Supplier;

class OverAllReport extends BaseComponent
{
    public $reports;

    public function mount()
    {
        $this->reports = $this->currentUser()->suppliers
            ->sortByDesc(fn (Supplier $supplier) => $supplier->reports()->orderByDesc('updated_at')->first()?->updated_at)
            ->map(function (Supplier $supplier) {
            return [
                'supplier' => $supplier,
                'report' => $supplier->reports()->orderByDesc('updated_at')->first()
            ];
        });
    }

    public function render()
    {
        return view('livewire.over-all-report');
    }
}
