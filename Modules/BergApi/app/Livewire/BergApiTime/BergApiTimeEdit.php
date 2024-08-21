<?php

namespace Modules\BergApi\Livewire\BergApiTime;

use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Livewire\Component;
use Modules\BergApi\Models\BergApiTime;

class BergApiTimeEdit extends Component
{
    public BergApiTime $bergApiTime;

    public $time;

    public function mount(): void
    {
        $this->time = $this->bergApiTime->time;
    }

    public function destroy(): void
    {
        $this->bergApiTime->delete();
        $this->dispatch('delete-time')->component(BergApiTimeIndex::class);
    }

    public function render(): View|Application|Factory|\Illuminate\View\View|\Illuminate\Contracts\Foundation\Application
    {
        return view('bergapi::livewire.berg-api-time.berg-api-time-edit');
    }
}
