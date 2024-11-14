<?php

namespace Modules\Assembly\Livewire\AssemblySettings;

use App\Livewire\ModuleComponent;

class AssemblySettingsIndex extends ModuleComponent
{
    public function render()
    {
        if (!$this->user()->can('update-assembly')) {
            abort(403);
        }

        return view('assembly::livewire.assembly-settings.assembly-settings-index');
    }
}
