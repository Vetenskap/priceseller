<?php

namespace App\Livewire\Traits;

trait WithFilters
{
    public function updatedFilters()
    {
        request()->merge(['filters' => $this->filters]);
    }
}
