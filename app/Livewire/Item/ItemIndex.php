<?php

namespace App\Livewire\Item;

use App\Jobs\Export;
use App\Jobs\Import;
use App\Livewire\Traits\WithFilters;
use App\Livewire\Traits\WithJsNotifications;
use App\Livewire\Traits\WithSubscribeNotification;
use App\Models\User;
use App\Services\Item\ItemService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;

class ItemIndex extends Component
{
    use WithFileUploads, WithJsNotifications, WithFilters, WithSubscribeNotification;

    /** @var TemporaryUploadedFile $file */
    public $file;
    public User $user;

    public function mount()
    {
        $this->user = auth()->user();
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
        $path = $this->file->storeAs(ItemService::PATH, $uuid . '.' . $ext);

        if (!Storage::exists($path)) {
            $this->dispatch('livewire-upload-error');
            return;
        }

        Import::dispatch($uuid, $ext, auth()->user(), ItemService::class);

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
