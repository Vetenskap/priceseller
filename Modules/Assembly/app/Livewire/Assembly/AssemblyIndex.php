<?php

namespace Modules\Assembly\Livewire\Assembly;

use App\Livewire\ModuleComponent;
use Livewire\Component;

class AssemblyIndex extends ModuleComponent
{
    public $headingLevel = '3';

    public $headingButton = '3';

    public $selectedField;

    public $fields = [
        'Код клиента',
        'Артикул поставщика'
    ];

    public $selectedFields = [];

    public function addField(): void
    {
        $this->selectedFields[$this->selectedField] = [
            'level' => '3',
            'color' => '#0BFF75'
        ];
    }


    public function render()
    {
        return view('assembly::livewire.assembly.assembly-index', [
            'modules' => $this->getEnabledModules()
        ]);
    }
}
