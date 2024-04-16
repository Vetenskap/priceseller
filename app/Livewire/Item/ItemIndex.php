<?php

namespace App\Livewire\Item;

use App\Livewire\Components\Toast;
use App\Models\Item;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithFileUploads;

class ItemIndex extends Component
{
    use WithFileUploads;

    public $items;

    public $table;

    #[On('livewire-upload-error')]
    public function err()
    {
        $this->js((new Toast('Ошибка', 'Не удалось загрузить файл'))->danger());
    }

    public function mount()
    {
        $this->items = Item::where('user_id', auth()->user()->id)->get();
    }

    public function save()
    {
        $this->table->store('tables');
    }

    public function render()
    {
        return view('livewire.item.item-index');
    }
}
