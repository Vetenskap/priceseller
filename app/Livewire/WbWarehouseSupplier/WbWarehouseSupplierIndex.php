<?php

namespace App\Livewire\WbWarehouseSupplier;

use App\Livewire\BaseComponent;
use App\Models\WbWarehouse;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Validation\Rule;

class WbWarehouseSupplierIndex extends BaseComponent
{
    public WbWarehouse $warehouse;

    public $supplier_id;

    public function placeholder(): string
    {
        return <<<'HTML'
        <div>
            <flux:icon.loading />
        </div>
        HTML;
    }

    public function store(): void
    {
        $this->validate();

        $this->authorizeForUser($this->user(), 'create', $this->warehouse->market);

        $this->warehouse->suppliers()->create([
            'supplier_id' => $this->supplier_id
        ]);
    }

    public function rules(): array
    {
        return [
            'supplier_id' => [
                'required',
                'exists:suppliers,id',
                Rule::unique('wb_warehouse_suppliers', 'supplier_id')
                    ->where('wb_warehouse_id', $this->warehouse->id)
            ]
        ];
    }

    public function render(): Factory|Application|View|\Illuminate\View\View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.wb-warehouse-supplier.wb-warehouse-supplier-index');
    }
}
