<?php

namespace App\Livewire\Item;

use App\Livewire\BaseComponent;
use App\Livewire\Components\Toast;
use App\Livewire\Forms\Item\ItemPostForm;
use App\Livewire\Traits\WithJsNotifications;
use App\Models\Item;

class ItemEdit extends BaseComponent
{
    use WithJsNotifications;

    public ItemPostForm $form;

    public Item $item;

    public $backRoute;

    public function redirectBack()
    {
        session()->flash('selected-item', $this->item->getKey());

        $this->redirect($this->backRoute);
    }

    public function save()
    {
        $this->authorize('update', $this->item);

        $result = $this->form->update();

        if (!$result->get('status')) {
            $this->js((new Toast('Ошибка', $result->get('message')))->danger());
        } else {
            $this->addSuccessSaveNotification();
        }
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
        $this->backRoute = \url()->previous();
    }

    public function render()
    {
        $this->authorize('view', $this->item);

        return view('livewire.item.item-edit');
    }
}
