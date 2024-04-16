<?php

namespace App\Livewire\Forms\Item;

use App\Models\Item;
use Livewire\Form;

class ItemPostForm extends Form
{
    public ?Item $item;

    public $ms_uuid;

    public $code;

    public $article_supplier;

    public $brand;

    public $article_manufactor;

    public $article_manufactor_brand;

    public $multiplicity;

    public function setItem(Item $item)
    {
        $this->item = $item;
        $this->ms_uuid = $item->ms_uuid;
        $this->code = $item->code;
        $this->article_supplier = $item->article_supplier;
        $this->brand = $item->brand;
        $this->article_manufactor = $item->article_manufactor;
        $this->article_manufactor_brand = $item->article_manufactor_brand;
        $this->multiplicity = $item->multiplicity;
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
