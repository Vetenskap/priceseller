<?php

namespace App\Livewire\OzonMarket;

use App\Livewire\Components\Toast;
use App\Livewire\Forms\OzonMarket\OzonMarketPostForm;
use App\Models\OzonMarket;
use App\Services\OzonMarketService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Attributes\On;
use Livewire\Attributes\Session;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;

class OzonMarketEdit extends Component
{
    use WithFileUploads;

    public OzonMarketPostForm $form;

    public OzonMarket $market;

    #[Session]
    public $selectedTab = 'main';

    /** @var TemporaryUploadedFile $table */
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

    public function export()
    {
        ini_set('max_execution_time', 1200); // TODO remove

        $service = new OzonMarketService($this->market);
        $path = $service->exportItems();

        $date = now()->toDateTimeString();
        return response()->download(Storage::disk('public')->path($path), "{$this->market->name}_{$date}.xlsx");
    }

    public function import()
    {
        $uuid = Str::uuid();
        $report = $this->market->importReports()->create([
            'uuid' => $uuid,
            'status' => 2,
            'message' => 'В процессе'
        ]);
        $path = $this->table->storeAs('users/ozon', $uuid . '.' . $this->table->getClientOriginalExtension(), 'public');
        $service = new OzonMarketService($this->market);
        $result = $service->importItems($path);
        $report->update([
            'correct' => $result->get('correct'),
            'error' => $result->get('error'),
            'status' => 0,
            'message' => 'Импорт завершён'
        ]);

    }

    public function relationshipsAndCommissions()
    {
        ini_set('max_execution_time', 1200); // TODO remove

        $report = $this->market->importReports()->create([
            'status' => 2,
            'message' => 'В процессе',
        ]);

        $service = new OzonMarketService($this->market);
        $result = $service->directRelationships(collect($this->only(['shipping_processing', 'min_price', 'min_price_percent'])));
        $report->update([
            'correct' => $result->get('correct'),
            'error' => $result->get('error'),
            'status' => 0,
            'message' => 'Импорт завершён',
        ]);
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
