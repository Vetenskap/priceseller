<?php

namespace App\Livewire\Forms\Item;

use App\Models\Item;
use Livewire\Form;

class ItemPostForm extends Form
{
    public ?Item $item;

    public $ms_uuid;

    public $code;

    public $article;

    public $brand;

    public $multiplicity;

    public $name;

    public $supplier_id;

    public function setItem(Item $item)
    {
        $this->item = $item;
        $this->ms_uuid = $item->ms_uuid;
        $this->code = $item->code;
        $this->brand = $item->brand;
        $this->multiplicity = $item->multiplicity;
        $this->article = $item->article;
        $this->name = $item->name;
        $this->supplier_id = $item->supplier_id;
    }

    public function update()
    {
        $this->item->update($this->except('item'));
    }

    public function delete()
    {
        $this->item->delete();
    }
}
