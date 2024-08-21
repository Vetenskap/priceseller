<?php

namespace Modules\SamsonApi\Livewire\SamsonApiTime;

use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Livewire\Component;
use Modules\SamsonApi\Models\SamsonApiTime;

class SamsonApiTimeEdit extends Component
{
    public SamsonApiTime $samsonApiTime;

    public $time;

    public function mount(): void
    {
        $this->time = $this->samsonApiTime->time;
    }

    public function destroy(): void
    {
        $this->samsonApiTime->delete();
        $this->dispatch('delete-time')->component(SamsonApiTimeIndex::class);
    }

    public function render(): View|Application|Factory|\Illuminate\View\View|\Illuminate\Contracts\Foundation\Application
    {
        return view('samsonapi::livewire.samson-api-time.samson-api-time-edit');
    }
}
