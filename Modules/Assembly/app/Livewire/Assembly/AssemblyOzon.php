<?php

namespace Modules\Assembly\Livewire\Assembly;

use App\HttpClient\OzonClient\OzonClient;
use App\HttpClient\OzonClient\Resources\FBS\PostingUnfulfilled\Posting;
use App\HttpClient\OzonClient\Resources\FBS\PostingUnfulfilled\PostingUnfulfilledList;
use App\Livewire\BaseComponent;
use App\Livewire\Traits\WithSort;
use App\Models\OzonWarehouse;
use Illuminate\Support\Collection;

class AssemblyOzon extends BaseComponent
{
    use WithSort;

    public $fields = [];

    public $mainFields = [];

    public $additionalFields = [];

    public ?Collection $postings = null;

    public OzonWarehouse $warehouse;

    public function updatedSortBy(): void
    {
        $this->postings = $this->postings->tap(fn (Collection $postings) => $this->sortBy ? ($this->sortDirection === 'asc' ? $postings->sortBy($this->sortBy) : $postings->sortByDesc($this->sortBy)) : $postings);
    }

    public function updatedSortDirection()
    {

    }

    public function mount()
    {
        $this->fields = $this->currentUser()
            ->assemblyProductSettings()
            ->where('market', 'ozon')
            ->whereNot('type', 'main')
            ->where('additional', false)
            ->orderBy('index')
            ->get()
            ->pluck(null, 'field')
            ->toArray();

        $this->additionalFields = $this->currentUser()
            ->assemblyProductSettings()
            ->where('market', 'ozon')
            ->where('additional', true)
            ->get()
            ->pluck(null, 'field')
            ->toArray();

        $this->mainFields = $this->currentUser()
            ->assemblyProductSettings()
            ->where('market', 'ozon')
            ->where('type', 'main')
            ->get()
            ->pluck(null, 'field')
            ->toArray();

        $this->loadOrders();
    }

    public function createLabel($postingNumber)
    {
        $data = [
            "posting_number" => [$postingNumber]
        ];

        $client = new OzonClient($this->warehouse->market->api_key, $this->warehouse->market->client_id);
        $response = $client->post('/v2/posting/fbs/package-label', $data);

        if ($response->successful() && $response->header('Content-Type') === 'application/pdf') {
            $pdfBase64 = base64_encode($response->body());

            // Передача base64-данных на клиентскую сторону
            $this->dispatch('openPdf', ['pdfBase64' => $pdfBase64]);
        } else {
            session()->flash('error', 'Не удалось загрузить PDF файл.');
        }
    }

    public function loadOrders(): void
    {
        $list = new PostingUnfulfilledList();
        $list->setFilterCutoffFrom(now());
        $list->setFilterCutoffTo(now()->addDays(10));
        $list->setWarehouseId([$this->warehouse->warehouse_id]);

        $postings = $list->next($this->warehouse->market->api_key, $this->warehouse->market->client_id);

        $this->postings = $postings->map(fn(Posting $posting) => $posting->toCollection($this->warehouse->market));
    }

    public function render()
    {
        return view('assembly::livewire.assembly.assembly-ozon');
    }
}