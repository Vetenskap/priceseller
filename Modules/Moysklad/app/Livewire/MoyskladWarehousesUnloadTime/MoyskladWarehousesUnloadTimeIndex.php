<?php

namespace Modules\Moysklad\Livewire\MoyskladWarehousesUnloadTime;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;
use Modules\Moysklad\Models\Moysklad;

class MoyskladWarehousesUnloadTimeIndex extends Component
{
    use WithPagination;

    public Moysklad $moysklad;
    public $time;

    public function rules(): array
    {
        return [
            'time' => ['required', 'date_format:H:i', Rule::unique('moysklad_warehouses_unload_times', 'time')->where('moysklad_id', $this->moysklad->id)],
        ];
    }

    public function store(): void
    {
        $this->validate();

        $this->moysklad->warehousesUnloadTimes()->create($this->only(['time']));
    }

    public function destroy($id): void
    {
        $time = $this->moysklad->warehousesUnloadTimes()->findOrFail($id);
        $time->delete();
    }

    #[Computed]
    public function times(): LengthAwarePaginator
    {
        return $this->moysklad->warehousesUnloadTimes()->paginate();
    }

    public function render()
    {
        return view('moysklad::livewire.moysklad-warehouses-unload-time.moysklad-warehouses-unload-time-index');
    }
}
