<?php

namespace App\Livewire\Item;

use App\Livewire\BaseComponent;

class ItemAttributeDialogForm extends BaseComponent
{
    public $name;
    public $type;

    protected $rules = [
        'name' => 'required|string',
        'type' => 'required|string',
    ];

    public function submit(): void
    {
        if (!$this->user()->can('update-items')) {
            abort(403);
        }

        $this->validate();

        $this->currentUser()->itemAttributes()->updateOrCreate([
            'name' => $this->name
        ], [
            'name' => $this->name,
            'type' => $this->type
        ]);

        $this->reset(['name', 'type']);

        $this->dispatch('create_item_attribute')->component(ItemEdit::class);

        \Flux::modal('create-item-attribute')->close();

    }

    public function render()
    {
        if (!$this->user()->can('update-items')) {
            abort(403);
        }

        return view('livewire.item.item-attribute-dialog-form');
    }
}
