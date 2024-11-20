<?php

namespace App\Livewire\SupplierReport;

use App\Livewire\BaseComponent;
use App\Livewire\Traits\WithJsNotifications;
use App\Models\Supplier;
use Livewire\Attributes\Computed;
use Livewire\WithPagination;

class SupplierReportIndex extends BaseComponent
{
    use WithPagination;

    public Supplier $supplier;

    public function getListeners(): array
    {
        return [
            'echo:notification.' . $this->currentUser()->id . ',.notify' => 'render',
        ];
    }

    public $sortBy = 'updated_at';
    public $sortDirection = 'desc';

    public function sort($column) : void
    {
        if ($this->sortBy === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $column;
            $this->sortDirection = 'asc';
        }
    }

    #[Computed]
    public function reports()
    {
        return $this->supplier
            ->reports()
            ->tap(fn ($query) => $this->sortBy ? $query->orderBy($this->sortBy, $this->sortDirection) : $query)
            ->paginate();
    }

    public function render()
    {
        return view('livewire.supplier-report.supplier-report-index');
    }
}
