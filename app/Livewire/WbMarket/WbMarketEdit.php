<?php

namespace App\Livewire\WbMarket;

use App\Components\FileParse;
use App\Livewire\Components\Toast;
use App\Livewire\Forms\WbMarket\WbMarketPostForm;
use App\Models\WbMarket;
use Livewire\Attributes\On;
use Livewire\Attributes\Session;
use Livewire\Component;
use Livewire\WithFileUploads;

class WbMarketEdit extends Component
{
    use WithFileUploads;

    public WbMarketPostForm $form;

    public WbMarket $market;

    #[Session]
    public $selectedTab = 'main';

    public $table;

    #[Session]
    public $sales_percent = null;

    #[Session]
    public $min_price = null;

    #[Session]
    public $retail_markup_percent = null;

    #[Session]
    public $package = null;

    #[On('livewire-upload-error')]
    public function err()
    {
        $this->js((new Toast('Ошибка', 'Не удалось загрузить файл'))->danger());
    }

    public function saveFile()
    {
        $path = storage_path('app\\'.$this->table->store('test'));

        $handler = new FileParse($path);

        $handler->start(function ($data) {
            dd($data);
        });
    }

    public function mount()
    {
        $this->form->setMarket($this->market);
    }

    public function save()
    {
        $this->authorize('update', $this->market);

        $this->form->update();
    }

    public function destroy()
    {
        $this->authorize('delete', $this->market);

        $this->market->delete();

        $this->redirectRoute('ozon', navigate: true);
    }
}
