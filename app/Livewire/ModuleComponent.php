<?php

namespace App\Livewire;

use App\Models\Module;
use App\Models\UserModule;
use Illuminate\Support\Collection;

class ModuleComponent extends BaseComponent
{
    public $changeOpen = [];

    public function mount()
    {
        $this->changeOpen = $this->currentUser()->modules->mapWithKeys(fn (UserModule $userModule) => [$userModule->module_id => (bool) $userModule->enabled])->toArray();
    }

    public function updatedChangeOpen(): void
    {
        foreach ($this->changeOpen as $key => $value) {
            $userModule = $this->currentUser()->modules()->where('module_id', $key)->first();

            if ($userModule) {
                $userModule->enabled = $value;
                $userModule->save();
            } else {
                $this->currentUser()->modules()->create([
                    'module_id' => $key,
                    'enabled' => true
                ]);
            }
        }

    }

    public function getEnabledModules(): Collection
    {
        $enabledModules = [];

        foreach (\Module::allEnabled() as $name => $module) {
            $enabledModules[] = $name;
        }

        return Module::whereIn('name', $enabledModules)->get();
    }

}
