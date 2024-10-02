<?php

namespace App\Livewire\Item;

use Livewire\Component;

class ItemAttributeDialogForm extends Component
{
    public $name;
    public $type;

    protected $rules = [
        'name' => 'required|string',
        'type' => 'required|string',
    ];

    public function submit(): void
    {
        $this->validate();

        auth()->user()->itemAttributes()->updateOrCreate([
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
        return view('livewire.item.item-attribute-dialog-form');
    }
}
