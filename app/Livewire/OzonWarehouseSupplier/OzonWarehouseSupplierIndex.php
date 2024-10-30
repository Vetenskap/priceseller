<?php

namespace App\Livewire\OzonWarehouseSupplier;

use App\Livewire\BaseComponent;
use App\Models\OzonWarehouse;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Validation\Rule;

class OzonWarehouseSupplierIndex extends BaseComponent
{
    public OzonWarehouse $warehouse;

    public $supplier_id;

    public function store(): void
    {
        $this->validate();

        $this->authorizeForUser($this->user(), 'update', $this->warehouse->market);

        $this->warehouse->suppliers()->create([
            'supplier_id' => $this->supplier_id
        ]);
    }

    public function placeholder(): string
    {
        return <<<'HTML'
        <div>
            <flux:icon.loading />
        </div>
        HTML;
    }

    public function rules(): array
    {
        return [
            'supplier_id' => [
                'required',
                'exists:suppliers,id',
                Rule::unique('ozon_warehouse_suppliers', 'supplier_id')
                    ->where('ozon_warehouse_id', $this->warehouse->id)
            ]
        ];
    }

    public function render(): View|Application|Factory|\Illuminate\View\View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.ozon-warehouse-supplier.ozon-warehouse-supplier-index');
    }
}
