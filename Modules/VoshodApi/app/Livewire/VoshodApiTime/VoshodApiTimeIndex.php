<?php

namespace Modules\VoshodApi\Livewire\VoshodApiTime;

use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Livewire\Attributes\On;
use Livewire\Component;
use Modules\VoshodApi\Models\VoshodApi;

class VoshodApiTimeIndex extends Component
{
    public VoshodApi $voshodApi;

    public $time;

    public function rules(): array
    {
        return [
            'time' => 'required|date_format:H:i'
        ];
    }

    public function store(): void
    {
        if ($this->voshodApi->times()->count() >= 3) {
            abort(403);
        }

        $this->validate();

        $this->voshodApi->times()->updateOrCreate([
            'time' => $this->time
        ], [
            'time' => $this->time
        ]);

    }

    public function destroy($id): void
    {
        $time = $this->voshodApi->times()->findOrFail($id);
        $time->delete();
    }

    public function render(): Factory|Application|View|\Illuminate\View\View|\Illuminate\Contracts\Foundation\Application
    {
        return view('voshodapi::livewire.voshod-api-time.voshod-api-time-index');
    }
}
