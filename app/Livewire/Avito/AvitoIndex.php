<?php

namespace App\Livewire\Avito;

use Livewire\Component;

class AvitoIndex extends Component
{
    public function render()
    {
        logger('Тест');

        return view('livewire.avito.avito-index');
    }
}
