<?php

namespace App\Livewire\Forms\Item;

use App\Livewire\Components\Toast;
use App\Models\Item;
use Illuminate\Support\Collection;
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

    public $unload_wb;

    public $unload_ozon;

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
        $this->unload_ozon = $item->unload_ozon;
        $this->unload_wb = $item->unload_wb;
    }

    public function update(): Collection
    {
        if (!$this->supplier_id) {
            return collect(['status' => false, 'message' => 'Не выбран поставщик']);
        }

        $this->item->update($this->except('item'));

        return collect(['status' => true, 'message' => '']);
    }

    public function delete()
    {
        $this->item->delete();
    }
}
