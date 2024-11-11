<?php

namespace Modules\BergApi\Livewire\BergApiTime;

use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Livewire\Attributes\On;
use Livewire\Component;
use Modules\BergApi\Models\BergApi;

class BergApiTimeIndex extends Component
{
    public BergApi $bergApi;

    public $time;

    public function rules(): array
    {
        return [
            'time' => 'required|date_format:H:i'
        ];
    }

    public function store(): void
    {
        if ($this->bergApi->times()->count() >= 3) {
            abort(403);
        }

        $this->validate();

        $this->bergApi->times()->updateOrCreate([
            'time' => $this->time
        ], [
            'time' => $this->time
        ]);

    }

    public function destroy($id): void
    {
        $time = $this->bergApi->times()->findOrFail($id);
        $time->delete();
    }

    public function render(): View|Application|Factory|\Illuminate\View\View|\Illuminate\Contracts\Foundation\Application
    {
        return view('bergapi::livewire.berg-api-time.berg-api-time-index');
    }
}
