<?php

namespace App\Livewire\Traits;

trait WithFilters
{
    public array $filters = [];

    public function updatedFilters(): void
    {
        request()->merge(['filters' => $this->filters]);
    }
}
