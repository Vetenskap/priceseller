<?php

namespace App\Livewire\Item;

use App\Livewire\BaseComponent;
use App\Livewire\Components\Toast;
use App\Livewire\Forms\Item\ItemPostForm;
use App\Livewire\Traits\WithJsNotifications;
use App\Livewire\Traits\WithSaveButton;
use App\Models\Item;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;

#[Title('Товары')]
class ItemEdit extends BaseComponent
{
    use WithJsNotifications, WithSaveButton;

    public ItemPostForm $form;

    public Item $item;

    public $backRoute = 'items';

    public function redirectBack(): void
    {
        session()->flash('selected-item', $this->item->getKey());

        $this->redirect($this->backRoute);
    }

    public function update(): void
    {
        $this->authorizeForUser($this->user(), 'update', $this->item);

        $result = $this->form->update();

        if (!$result->get('status')) {
            $this->js((new Toast('Ошибка', $result->get('message')))->danger());
        } else {
            $this->addSuccessSaveNotification();
            $this->hideSaveButton();
        }
    }

    public function destroy(): void
    {
        $this->authorizeForUser($this->user(), 'delete', $this->item);

        $this->form->delete();

        $this->redirectRoute('items');
    }

    #[On('create_item_attribute')]
    public function mount(): void
    {
        $this->form->setItem($this->item);
        $this->backRoute = \url()->previous();
    }

    public function deleteAttribute(array $mainAttribute): void
    {
        $this->authorizeForUser($this->user(), 'update', $this->item);

        $attribute = $this->currentUser()->itemAttributes()->where('id', $mainAttribute['id'])->first();
        $attribute->delete();
    }

    public function render(): View|Application|Factory|\Illuminate\View\View|\Illuminate\Contracts\Foundation\Application
    {
        $this->authorizeForUser($this->user(), 'view', $this->item);

        return view('livewire.item.item-edit');
    }
}
