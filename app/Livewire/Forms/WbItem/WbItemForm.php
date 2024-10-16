<?php

namespace App\Livewire\Forms\WbItem;

use App\Models\WbItem;
use Livewire\Attributes\Validate;
use Livewire\Form;

class WbItemForm extends Form
{
    public ?WbItem $wbItem = null;

    #[Validate]
    public $nm_id;
    #[Validate]
    public $vendor_code;
    #[Validate]
    public $sku;
    #[Validate]
    public $sales_percent;
    #[Validate]
    public $min_price;
    #[Validate]
    public $retail_markup_percent;
    #[Validate]
    public $package;
    #[Validate]
    public $volume;
    #[Validate]
    public $wb_market_id;
    #[Validate]
    public $wbitemable_id;
    #[Validate]
    public $wbitemable_type;

    public function setWbItem(WbItem $wbItem): void
    {
        $this->wbItem = $wbItem;
        $this->nm_id = $wbItem->nm_id;
        $this->vendor_code = $wbItem->vendor_code;
        $this->sku = $wbItem->sku;
        $this->sales_percent = $wbItem->sales_percent;
        $this->min_price = $wbItem->min_price;
        $this->retail_markup_percent = $wbItem->retail_markup_percent;
        $this->package = $wbItem->package;
        $this->volume = $wbItem->volume;
        $this->wb_market_id = $wbItem->wb_market_id;
        $this->wbitemable_id = $wbItem->wbitemable_id;
        $this->wbitemable_type = $wbItem->wbitemable_type;
    }

    public function rules(): array
    {
        return [
            'nm_id' => ['nullable', 'integer'],
            'vendor_code' => ['required'],
            'sku' => ['nullable'],
            'sales_percent' => ['nullable', 'numeric', 'min:0'],
            'min_price' => ['nullable', 'integer', 'min:0'],
            'retail_markup_percent' => ['nullable', 'numeric', 'min:0'],
            'package' => ['nullable', 'numeric', 'min:0'],
            'volume' => ['nullable', 'numeric'],
            'wbitemable_id' => ['required', 'uuid'],
            'wbitemable_type' => ['required'],
            'wb_market_id' => ['required', 'uuid', 'exists:wb_markets,id'],
        ];
    }

    public function update(): void
    {
        $this->validate();

        $this->wbItem->update($this->except('wbItem'));
    }

    public function destroy(): void
    {
        $this->wbItem->delete();
    }
}
