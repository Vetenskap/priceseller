<?php

namespace App\Livewire;

use App\Models\Organization;
use Livewire\Attributes\Session;
use Livewire\Attributes\Url;
use Livewire\Component;

class Order extends Component
{
    #[Url]
    public $organizationId;

    public ?Organization $organization = null;

    public function mount()
    {
        $this->organization = Organization::find($this->organizationId);
    }

    public function render()
    {
        return view('livewire.order');
    }
}
