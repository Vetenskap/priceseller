<?php

namespace App\Livewire\Item;

use App\Jobs\Export;
use App\Jobs\Import;
use App\Jobs\Moysklad\ApiAssortment;
use App\Jobs\Moysklad\ExcelAssortment;
use App\Livewire\Traits\WithFilters;
use App\Livewire\Traits\WithJsNotifications;
use App\Livewire\Traits\WithSubscribeNotification;
use App\Models\User;
use App\Services\Item\ItemService;
use App\Services\MoyskladService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Attributes\Session;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;

class ItemIndex extends Component
{
    use WithFileUploads, WithJsNotifications, WithFilters, WithSubscribeNotification;

    /** @var TemporaryUploadedFile $file */
    public $file;
    public User $user;

    public $selectedTab;

    // Мой склад
    public $itemInfo;
    #[Session]
    public $selectedCode = null;
    #[Session]
    public $selectedName = null;
    #[Session]
    public $selectedArticle = null;
    #[Session]
    public $selectedMultiplicity = null;

    #[Session]
    public $selectedBrand = null;

    public function importApi()
    {
        ApiAssortment::dispatch(auth()->user()->moysklad, collect([
            'code' => $this->selectedCode,
            'name' => $this->selectedName,
            'article' => $this->selectedArticle,
            'multiplicity' => $this->selectedMultiplicity,
            'brand' => $this->selectedBrand,
        ]));
    }

    public function mount()
    {
        $this->user = auth()->user();

        if ($this->user->moysklad) {
            $service = new MoyskladService($this->user->moysklad);
            $service->setClient();

            $this->itemInfo = $service->getItemInfo();
            $this->selectedCode = $this->selectedCode ?? $this->itemInfo->first()['id'];
            $this->selectedName = $this->selectedName ?? $this->itemInfo->first()['id'];
            $this->selectedArticle = $this->selectedArticle ?? $this->itemInfo->first()['id'];
            $this->selectedMultiplicity = $this->selectedMultiplicity ?? $this->itemInfo->first()['id'];
            $this->selectedBrand = $this->selectedBrand ?? $this->itemInfo->first()['id'];
        }
    }

    public function export()
    {
        Export::dispatch(auth()->user(), ItemService::class);

        $this->dispatch('items-export-report-created');
    }

    public function import()
    {
        $uuid = Str::uuid();
        $ext = $this->file->getClientOriginalExtension();

        if ($this->selectedTab === 'base') {

            $path = $this->file->storeAs(ItemService::PATH, $uuid . '.' . $ext);

            if (!Storage::exists($path)) {
                $this->dispatch('livewire-upload-error');
                return;
            }

            Import::dispatch($uuid, $ext, auth()->user(), ItemService::class);
        } else {

            $path = $this->file->storeAs(MoyskladService::PATH, $uuid . '.' . $ext);

            if (!Storage::exists($path)) {
                $this->dispatch('livewire-upload-error');
                return;
            }

            $code = $this->itemInfo->firstWhere('id', $this->selectedCode)['name'];
            $name = $this->itemInfo->firstWhere('id', $this->selectedName)['name'];
            $article = $this->itemInfo->firstWhere('id', $this->selectedArticle)['name'];
            $multiplicity = $this->itemInfo->firstWhere('id', $this->selectedMultiplicity)['name'];
            $brand = $this->itemInfo->firstWhere('id', $this->selectedBrand)['name'];

            ExcelAssortment::dispatch($uuid, $ext, auth()->user()->moysklad, collect([
                'code' => Str::isUuid($this->itemInfo->firstWhere('id', $this->selectedCode)['id']) ? "Доп. поле: $code" : $code,
                'name' => Str::isUuid($this->itemInfo->firstWhere('id', $this->selectedName)['id']) ? "Доп. поле: $name" : $name,
                'article' => Str::isUuid($this->itemInfo->firstWhere('id', $this->selectedArticle)['id']) ? "Доп. поле: $article" : $article,
                'multiplicity' => Str::isUuid($this->itemInfo->firstWhere('id', $this->selectedMultiplicity)['id']) ? "Доп. поле: $multiplicity" : $multiplicity,
                'brand' => Str::isUuid($this->itemInfo->firstWhere('id', $this->selectedBrand)['id']) ? "Доп. поле: $brand" : $brand,
            ]));
        }

        $this->dispatch('items-import-report-created');
    }

    public function render()
    {
        $items = auth()
            ->user()
            ->items()
            ->orderByDesc('updated_at')
            ->with('supplier')
            ->filters()
            ->paginate(50);

        return view('livewire.item.item-index', [
            'items' => $items
        ]);
    }
}
