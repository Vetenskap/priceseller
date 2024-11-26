<?php

namespace Modules\Assembly\Livewire\Assembly;

use App\HttpClient\OzonClient\OzonClient;
use App\HttpClient\OzonClient\Resources\FBS\PostingUnfulfilled\Posting;
use App\HttpClient\OzonClient\Resources\FBS\PostingUnfulfilled\PostingUnfulfilledList;
use App\HttpClient\OzonClient\Resources\FBS\PostingUnfulfilled\Product;
use App\Livewire\BaseComponent;
use App\Livewire\Traits\WithSort;
use App\Models\Item;
use App\Models\OzonWarehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

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
        if ($this->sortDirection === 'asc') {
            $this->postings = $this->postings->sortBy(function (Posting $posting) {
                try {
                    return $posting->{Str::camel('get' . $this->sortBy)}();
                } catch (\Error $e) {
                    try {
                        return $posting->getProducts()->sortBy(fn (Product $product) => $product->{Str::camel('get' . $this->sortBy)}())->first()->{Str::camel('get' . $this->sortBy)}();
                    } catch (\Error $e) {

                        try {
                            return $posting->getProducts()->sortBy(fn (Product $product) => $product->getAttribute()->{Str::camel('get' . $this->sortBy)}())->first()->getAttribute()->{Str::camel('get' . $this->sortBy)}();
                        } catch (\Error $e) {
                            if ($posting->getProducts()->first(fn (Product $product) => $product->getProduct()[$this->sortBy])) {
                                return $posting->getProducts()->sortBy(fn (Product $product) => $product->getProduct()[$this->sortBy])->first()[$this->sortBy];
                            } else {

                                /** @var Product $product */
                                $product = $posting->getProducts()->sortBy(function (Product $product) {
                                    if ($product->getProduct()?->itemable instanceof Item) {
                                        return $product->getProduct()?->itemable[$this->sortBy];
                                    } else {
                                        return $product->getProduct()?->itemable->items->sortBy(fn(Item $item) => $item[$this->sortBy])->first()[$this->sortBy];
                                    }
                                })->first();

                                if ($this->sortBy === 'all_stocks') {
                                    if ($product->getProduct()?->itemable instanceof Item) {
                                        return $product->getProduct()?->itemable->warehousesStocks()->sum('stock');
                                    } else {
                                        return $product->getProduct()?->itemable->items->sortBy(fn(Item $item) => $item->warehousesStocks()->sum('stock'))->first()->warehousesStocks()->sum('stock');
                                    }
                                }

                                if ($product->getProduct()?->itemable instanceof Item) {
                                    return $product->getProduct()?->itemable[$this->sortBy];
                                } else {
                                    return $product->getProduct()?->itemable->items->first()[$this->sortBy];
                                }
                            }
                        }
                    }
                }
            });
        } else {
            $this->postings = $this->postings->sortByDesc(function (Posting $posting) {
                try {
                    return $posting->{Str::camel('get' . $this->sortBy)}();
                } catch (\Error $e) {
                    try {
                        return $posting->getProducts()->sortByDesc(fn (Product $product) => $product->{Str::camel('get' . $this->sortBy)}())->first()->{Str::camel('get' . $this->sortBy)}();
                    } catch (\Error $e) {

                        try {
                            return $posting->getProducts()->sortByDesc(fn (Product $product) => $product->getAttribute()->{Str::camel('get' . $this->sortBy)}())->first()->getAttribute()->{Str::camel('get' . $this->sortBy)}();
                        } catch (\Error $e) {
                            if ($posting->getProducts()->first(fn (Product $product) => $product->getProduct()[$this->sortBy])) {
                                return $posting->getProducts()->sortByDesc(fn (Product $product) => $product->getProduct()[$this->sortBy])->first()[$this->sortBy];
                            } else {

                                /** @var Product $product */
                                $product = $posting->getProducts()->sortByDesc(function (Product $product) {
                                    if ($product->getProduct()?->itemable instanceof Item) {
                                        return $product->getProduct()?->itemable[$this->sortBy];
                                    } else {
                                        return $product->getProduct()?->itemable->items->sortByDesc(fn(Item $item) => $item[$this->sortBy])->first()[$this->sortBy];
                                    }
                                })->first();

                                if ($this->sortBy === 'all_stocks') {
                                    if ($product->getProduct()?->itemable instanceof Item) {
                                        return $product->getProduct()?->itemable->warehousesStocks()->sum('stock');
                                    } else {
                                        return $product->getProduct()?->itemable->items->sortByDesc(fn(Item $item) => $item->warehousesStocks()->sum('stock'))->first()->warehousesStocks()->sum('stock');
                                    }
                                }

                                if ($product->getProduct()->itemable instanceof Item) {
                                    return $product->getProduct()->itemable[$this->sortBy];
                                } else {
                                    return $product->getProduct()->itemable->items->first()[$this->sortBy];
                                }
                            }
                        }
                    }
                }
            });
        }
    }

    public function mount(Request $request)
    {
        $status = $request->query('status');
        $startDate = Carbon::createFromFormat('Y-m-d', $request->input('startDate'));
        $endDate = Carbon::createFromFormat('Y-m-d', $request->input('endDate'));

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

        $this->loadOrders($status, $startDate, $endDate);
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

    public function loadOrders(string $status, Carbon $startDate, Carbon $endDate): void
    {
        $list = new PostingUnfulfilledList($this->warehouse->market->api_key, $this->warehouse->market->client_id);
        $list->setFilterCutoffFrom($startDate);
        $list->setFilterCutoffTo($endDate);
        $list->setFilterStatus($status);
        $list->setWarehouseId([$this->warehouse->warehouse_id]);

        $postings = $list->next();

        $this->postings = $postings;
    }

    public function render()
    {
        if (!$this->user()->can('view-assembly')) {
            abort(403);
        }

        return view('assembly::livewire.assembly.assembly-ozon');
    }
}
