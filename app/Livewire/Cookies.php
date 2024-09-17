<?php

namespace App\Livewire;

use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Livewire\Component;

class Cookies extends Component
{
    public function acceptCookies(): void
    {
        session()->put('cookies', true);
    }

    public function rejectCookies(): void
    {
        session()->put('cookies', false);
    }

    public function render(): View|Application|Factory|\Illuminate\View\View|\Illuminate\Contracts\Foundation\Application
    {
        return view('livewire.cookies');
    }
}
