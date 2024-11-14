<?php

namespace Modules\Assembly\Livewire\Assembly;

use App\Livewire\ModuleComponent;

class AssemblyIndex extends ModuleComponent
{
    public $startDateOzon;
    public $endDateOzon;
    public $statusOzon = 'awaiting_packaging';

    public function mount()
    {
        $this->startDateOzon = now()->format('Y-m-d');
        $this->endDateOzon = now()->format('Y-m-d');
    }

    public function render()
    {
        return view('assembly::livewire.assembly.assembly-index', [
            'modules' => $this->getEnabledModules()
        ]);
    }
}
