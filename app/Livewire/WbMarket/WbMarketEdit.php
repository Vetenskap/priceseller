<?php

namespace App\Livewire\WbMarket;

use App\Livewire\Components\Toast;
use App\Livewire\Forms\WbMarket\WbMarketPostForm;
use App\Models\WbMarket;
use App\Services\MarketImportReportService;
use App\Services\WbMarketService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
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
        $path = storage_path('app\\' . $this->table->store('test'));

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

    public function export()
    {
        ini_set('max_execution_time', 1200); // TODO remove

        $service = new WbMarketService($this->market);
        $path = $service->exportItems();

        $date = now()->toDateTimeString();
        return response()->download(Storage::disk('public')->path($path), "{$this->market->name}_{$date}.xlsx");
    }

    public function import()
    {
        $uuid = Str::uuid();
        MarketImportReportService::newOrFirst($this->market);

        $path = $this->table->storeAs('users/wb', $uuid . '.' . $this->table->getClientOriginalExtension(), 'public');
        $service = new WbMarketService($this->market);
        $result = $service->importItems($path);
        MarketImportReportService::success($this->market, $result->get('correct'), $result->get('error'), $uuid);

    }

    public function relationshipsAndCommissions()
    {
        ini_set('max_execution_time', 1200); // TODO remove

        MarketImportReportService::newOrFirst($this->market);

        $service = new WbMarketService($this->market);
        $result = $service->directRelationships(collect($this->only(['package', 'retail_markup_percent', 'min_price', 'sales_percent'])));

        MarketImportReportService::success($this->market, $result->get('correct'), $result->get('error'));
    }
}
