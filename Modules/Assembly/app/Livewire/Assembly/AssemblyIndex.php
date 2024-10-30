<?php

namespace Modules\Assembly\Livewire\Assembly;

use App\Livewire\ModuleComponent;

class AssemblyIndex extends ModuleComponent
{
    public function render()
    {
        return view('assembly::livewire.assembly.assembly-index', [
            'modules' => $this->getEnabledModules()
        ]);
    }
}
