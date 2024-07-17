<?php

namespace App\Livewire\Item;

use App\Exports\ItemsExport;
use App\Jobs\Export;
use App\Jobs\Import;
use App\Livewire\BaseComponent;
use App\Livewire\Traits\WithFilters;
use App\Livewire\Traits\WithJsNotifications;
use App\Models\User;
use App\Services\Item\ItemService;
use App\Services\ItemsExportReportService;
use App\Services\ItemsImportReportService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Attributes\Url;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;

class ItemIndex extends BaseComponent
{
    use WithFileUploads, WithJsNotifications, WithFilters;

    /** @var TemporaryUploadedFile $file */
    public $file;
    public User $user;

    #[Url]
    public $tab = null;

    public function mount()
    {
        $this->user = auth()->user();
    }

    public function downloadTemplate()
    {
        return \Excel::download(new ItemsExport($this->user->id, true), "priceseller_шаблон.xlsx");
    }

    public function export()
    {
        if (ItemsExportReportService::get($this->user)) {
            $this->addJobAlready();
            return;
        }

        Export::dispatch(auth()->user(), ItemService::class);
        $this->addJobNotification();
    }

    public function import()
    {
        if (ItemsImportReportService::get($this->user)) {
            $this->addJobAlready();
            return;
        }

        $uuid = Str::uuid();
        $ext = $this->file->getClientOriginalExtension();
        $path = $this->file->storeAs(ItemService::PATH, $uuid . '.' . $ext);

        if (!Storage::exists($path)) {
            $this->dispatch('livewire-upload-error');
            return;
        }

        Import::dispatch($uuid, $ext, auth()->user(), ItemService::class);
        $this->addJobNotification();
    }

    public function render()
    {
        $items = auth()
            ->user()
            ->items()
            ->orderByDesc('updated_at')
            ->with('supplier')
            ->filters()
            ->paginate(10);

        return view('livewire.item.item-index', [
            'items' => $this->tab === 'list' ? $items : []
        ]);
    }
}
