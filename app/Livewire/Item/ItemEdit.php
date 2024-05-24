<?php

namespace App\Livewire\Item;

use App\Livewire\Forms\Item\ItemPostForm;
use App\Livewire\Traits\WithJsNotifications;
use App\Livewire\Traits\WithSubscribeNotification;
use App\Models\Item;
use Livewire\Component;

class ItemEdit extends Component
{
    use WithJsNotifications, WithSubscribeNotification;

    public ItemPostForm $form;

    public Item $item;

    public function save()
    {
        $this->authorize('update', $this->item);

        $this->form->update();

        $this->addSuccessSaveNotification();
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
