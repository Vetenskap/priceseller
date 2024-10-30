<?php

namespace App\Livewire\Traits;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Concerns\BuildsQueries;
use Illuminate\Database\Eloquent\Relations\HasMany;

trait WithSort
{
    public $sortBy = 'updated_at';
    public $sortDirection = 'desc';

    public function sort($column): void
    {
        if ($this->sortBy === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $column;
            $this->sortDirection = 'asc';
        }
    }

    public function tapQuery($query): LengthAwarePaginator
    {
        return $query
            ->tap(fn($query) => $this->sortBy ? $query->orderBy($this->sortBy, $this->sortDirection) : $query)
            ->paginate();
    }
}
