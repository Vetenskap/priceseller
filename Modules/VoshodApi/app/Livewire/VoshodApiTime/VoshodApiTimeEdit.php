<?php

namespace Modules\VoshodApi\Livewire\VoshodApiTime;

use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Livewire\Component;
use Modules\VoshodApi\Models\VoshodApiTime;

class VoshodApiTimeEdit extends Component
{
    public VoshodApiTime $voshodApiTime;

    public $time;

    public function mount(): void
    {
        $this->time = $this->voshodApiTime->time;
    }

    public function destroy(): void
    {
        $this->voshodApiTime->delete();
        $this->dispatch('delete-time')->component(VoshodApiTimeIndex::class);
    }

    public function render(): View|Application|Factory|\Illuminate\View\View|\Illuminate\Contracts\Foundation\Application
    {
        return view('voshodapi::livewire.voshod-api-time.voshod-api-time-edit');
    }
}
