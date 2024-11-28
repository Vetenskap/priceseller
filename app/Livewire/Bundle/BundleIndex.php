<?php

namespace App\Livewire\Bundle;

use App\Livewire\BaseComponent;
use App\Livewire\Traits\WithFilters;
use App\Models\Bundle;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use LaravelIdea\Helper\App\Models\_IH_Bundle_C;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\WithPagination;

#[Title('Комплекты')]
class BundleIndex extends BaseComponent
{
    use WithFilters, WithPagination, WithFilters;

    #[Computed]
    public function bundles(): LengthAwarePaginator|\Illuminate\Pagination\LengthAwarePaginator|array|_IH_Bundle_C
    {
        return $this->currentUser()
            ->bundles()
            ->with('items')
            ->filters()
            ->paginate();
    }

    public function destroy($id): void
    {
        $bundle = Bundle::find($id);

        $this->authorizeForUser($this->user(), 'delete', $bundle);

        $bundle->delete();
    }

    public function render(): View|Application|Factory|\Illuminate\View\View|\Illuminate\Contracts\Foundation\Application
    {
        if (!$this->user()->can('view-bundles')) {
            abort(403);
        }

        return view('livewire.bundle.bundle-index');
    }
}
