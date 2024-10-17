<?php

namespace App\Livewire\Bundle;

use App\Livewire\BaseComponent;
use App\Livewire\Traits\WithFilters;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\WithPagination;

#[Title('Комплекты')]
class BundleIndex extends BaseComponent
{
    use WithFilters, WithPagination;

    #[Computed]
    public function bundles()
    {
        return $this->currentUser()
            ->bundles()
            ->with('items')
            ->paginate();
    }

    public function render(): View|Application|Factory|\Illuminate\View\View|\Illuminate\Contracts\Foundation\Application
    {

        return view('livewire.bundle.bundle-index');
    }
}
