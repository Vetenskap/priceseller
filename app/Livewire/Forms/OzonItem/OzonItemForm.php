<?php

namespace App\Livewire\Forms\OzonItem;

use App\Models\OzonItem;
use Livewire\Attributes\Validate;
use Livewire\Form;

class OzonItemForm extends Form
{
    public ?OzonItem $ozonItem = null;

    #[Validate]
    public $product_id;
    #[Validate]
    public $offer_id;
    #[Validate]
    public $min_price_percent;
    #[Validate]
    public $min_price;
    #[Validate]
    public $shipping_processing;
    #[Validate]
    public $direct_flow_trans;
    #[Validate]
    public $deliv_to_customer;
    #[Validate]
    public $sales_percent;
    #[Validate]
    public $price_seller;
    #[Validate]
    public $ozon_market_id;
    #[Validate]
    public $ozonitemable_id;
    #[Validate]
    public $ozonitemable_type;

    public function setOzonItem(OzonItem $ozonItem): void
    {
        $this->ozonItem = $ozonItem;
        $this->product_id = $ozonItem->product_id;
        $this->offer_id = $ozonItem->offer_id;
        $this->min_price_percent = $ozonItem->min_price_percent;
        $this->min_price = $ozonItem->min_price;
        $this->shipping_processing = $ozonItem->shipping_processing;
        $this->direct_flow_trans = $ozonItem->direct_flow_trans;
        $this->deliv_to_customer = $ozonItem->deliv_to_customer;
        $this->sales_percent = $ozonItem->sales_percent;
        $this->price_seller = $ozonItem->price_seller;
        $this->ozon_market_id = $ozonItem->ozon_market_id;
        $this->ozonitemable_id = $ozonItem->ozonitemable_id;
        $this->ozonitemable_type = $ozonItem->ozonitemable_type;
    }

    public function rules(): array
    {
        return [
            'product_id' => ['nullable'],
            'offer_id' => ['required'],
            'min_price_percent' => ['nullable', 'integer', 'min:0'],
            'min_price' => ['nullable', 'integer', 'min:0'],
            'shipping_processing' => ['nullable', 'numeric', 'min:0'],
            'direct_flow_trans' => ['nullable', 'numeric', 'min:0'],
            'deliv_to_customer' => ['nullable', 'numeric', 'min:0'],
            'sales_percent' => ['nullable', 'integer', 'min:0'],
            'price_seller' => ['required', 'numeric', 'min:0'],
            'ozon_market_id' => ['required', 'uuid', 'exists:ozon_markets,id'],
            'ozonitemable_id' => ['required', 'uuid'],
            'ozonitemable_type' => ['required'],
        ];
    }

    public function update(): void
    {
        $this->validate();

        $this->ozonItem->update($this->except('ozonItem'));
    }

    public function destroy(): void
    {
        $this->ozonItem->delete();
    }
}
