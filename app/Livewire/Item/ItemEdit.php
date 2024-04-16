<?php

namespace App\Livewire\Item;

use App\Livewire\Forms\Item\ItemPostForm;
use App\Models\Item;
use Livewire\Component;

class ItemEdit extends Component
{
    public ItemPostForm $form;

    public Item $item;

    public function save()
    {
        $this->authorize('update', $this->item);

        $this->form->update();
    }

    public function destroy()
    {
        $this->authorize('delete', $this->item);

        $this->form->delete();

        $this->redirectRoute('items');
    }

    public function mount()
    {
        $this->form->setItem($this->item);
    }

    public function render()
    {
        return view('livewire.item.item-edit');
    }
}
