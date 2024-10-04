<?php

namespace App\Livewire\Item;

use App\Exports\ItemsExport;
use App\Jobs\Export;
use App\Jobs\Import;
use App\Livewire\BaseComponent;
use App\Livewire\Traits\WithFilters;
use App\Livewire\Traits\WithSort;
use App\Models\Item;
use App\Models\User;
use App\Services\Item\ItemService;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

#[Title('Товары')]
class ItemIndex extends BaseComponent
{
    use WithFileUploads, WithFilters, WithPagination, WithSort;

    /** @var TemporaryUploadedFile $file */
    public $file;

    public User $user;

    public function edit($id): void
    {
        $this->redirect(route('item-edit', ['item' => $id]));
    }

    public function destroy($id): void
    {
        $item = Item::find($id);

        $this->authorize('delete', $item);

        $item->delete();

        $this->addSuccessDeleteNotification();
    }

    #[Computed]
    public function items()
    {
        return auth()->user()
            ->items()
            ->with('supplier')
            ->filters()
            ->tap(fn($query) => $this->sortBy ? $query->orderBy($this->sortBy, $this->sortDirection) : $query)
            ->paginate();

    }

    public function mount(): void
    {
        $this->user = auth()->user();
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
        sleep(10);

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
        return view('livewire.item.item-index');
    }
}
