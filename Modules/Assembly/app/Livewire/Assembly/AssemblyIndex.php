<?php

namespace Modules\Assembly\Livewire\Assembly;

use App\Livewire\ModuleComponent;
use Modules\Assembly\Services\AssemblyWbService;

class AssemblyIndex extends ModuleComponent
{
    public $startDateOzon;
    public $endDateOzon;
    public $statusOzon = 'awaiting_packaging';

    public function mount()
    {
        $this->startDateOzon = now()->format('Y-m-d');
        $this->endDateOzon = now()->addYear()->format('Y-m-d');
    }

    public function destroyWbSupply($id)
    {
        $supply = \Modules\Assembly\Models\AssemblyWbSupply::findOrFail($id);
        $supply->delete();
    }

    public function loadSuppliesWb($marketId)
    {
        $market = $this->currentUser()->wbMarkets()->findOrFail($marketId);

        AssemblyWbService::loadSupplies($market);
    }

    public function ozonBarcodes()
    {
        return view('assembly::livewire.assembly.assembly-ozon-barcodes');
    }

    public function render()
    {
        if (!$this->user()->can('view-assembly')) {
            abort(403);
        }

        return view('assembly::livewire.assembly.assembly-index', [
            'modules' => $this->getEnabledModules()
        ]);
    }
}
