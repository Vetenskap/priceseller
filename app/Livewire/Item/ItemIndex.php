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
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Attributes\Title;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

#[Title('Товары')]
class ItemIndex extends BaseComponent
{
    use WithFileUploads, WithJsNotifications, WithFilters;

    /** @var TemporaryUploadedFile $file */
    public $file;

    public User $user;

    public $page;

    public function mount($page = 'list'): void
    {
        $this->user = auth()->user();
        $this->page = $page;
    }

    public function downloadTemplate(): BinaryFileResponse
    {
        return \Excel::download(new ItemsExport($this->user->id, true), "priceseller_шаблон.xlsx");
    }

    public function export(): void
    {
        $status = $this->checkTtlJob(Export::getUniqueId($this->user), Export::class);

        if ($status) Export::dispatch(auth()->user(), ItemService::class);
    }

    public function import(): void
    {
        $uuid = Str::uuid();
        $ext = $this->file->getClientOriginalExtension();
        $path = $this->file->storeAs(ItemService::PATH, $uuid . '.' . $ext);

        if (!Storage::exists($path)) {
            $this->dispatch('livewire-upload-error');
            return;
        }

        $status = $this->checkTtlJob(Import::getUniqueId($this->user), Import::class);

        if ($status) Import::dispatch($uuid, $ext, $this->user, ItemService::class);
    }

    public function render(): View|Application|Factory|\Illuminate\View\View|\Illuminate\Contracts\Foundation\Application
    {
        if ($this->page === 'list') {

            $items = auth()
                ->user()
                ->items()
                ->orderByDesc('updated_at')
                ->with('supplier')
                ->filters()
                ->paginate(10);

            return view('livewire.item.pages.item-list-page', [
                'items' => $items
            ]);

        } else if ($this->page === 'manage') {
            return view('livewire.item.pages.items-manage-page');
        }

        return view('livewire.item.item-index');
    }
}
