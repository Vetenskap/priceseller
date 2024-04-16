<?php

namespace App\Livewire\OzonMarket;

use App\Livewire\Components\Toast;
use App\Livewire\Forms\OzonMarket\OzonMarketPostForm;
use App\Models\OzonMarket;
use Livewire\Attributes\On;
use Livewire\Attributes\Session;
use Livewire\Component;

class OzonMarketEdit extends Component
{
    public OzonMarketPostForm $form;

    public OzonMarket $market;

    #[Session]
    public $selectedTab = 'main';

    public $table;

    #[Session]
    public $min_price_percent = null;

    #[Session]
    public $min_price = null;

    #[Session]
    public $shipping_processing = null;

    #[On('livewire-upload-error')]
    public function err()
    {
        $this->js((new Toast('Ошибка', 'Не удалось загрузить файл'))->danger());
    }

    public function saveFile()
    {

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
