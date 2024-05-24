<?php

namespace App\Livewire\Traits;

trait WithFilters
{
    public $filters = [];

    public function updatedFilters()
    {
        request()->merge(['filters' => $this->filters]);
    }
}
