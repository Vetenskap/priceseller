<?php

namespace App\Livewire\Forms;

use App\Models\EmailSupplierStockValue;
use Livewire\Attributes\Validate;
use Livewire\Form;

class EmailSupplierStockValuePostForm extends Form
{
    public EmailSupplierStockValue $stockValue;

    public $name;
    public $value;
    //
    public function setStockValue(\App\Models\EmailSupplierStockValue $stockValue)
    {
        $this->name = $stockValue->name;
        $this->value = $stockValue->value;
        $this->stockValue = $stockValue;
    }

    public function update()
    {
        $this->stockValue->update($this->except('stockValue'));
    }
}
