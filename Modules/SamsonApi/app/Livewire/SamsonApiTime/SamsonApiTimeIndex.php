<?php

namespace Modules\SamsonApi\Livewire\SamsonApiTime;

use App\Livewire\Traits\WithJsNotifications;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Livewire\Attributes\On;
use Livewire\Component;
use Modules\SamsonApi\Models\SamsonApi;

class SamsonApiTimeIndex extends Component
{
    public SamsonApi $samsonApi;

    public $time;

    public function rules(): array
    {
        return [
            'time' => 'required|date_format:H:i'
        ];
    }

    public function store(): void
    {
        if ($this->samsonApi->times()->count() >= 3) {
            abort(403);
        }

        $this->validate();

        $this->samsonApi->times()->updateOrCreate([
            'time' => $this->time
        ], [
            'time' => $this->time
        ]);

    }

    public function destroy($id): void
    {
        $time = $this->samsonApi->times()->findOrFail($id);
        $time->delete();
    }

    public function render(): View|Application|Factory|\Illuminate\View\View|\Illuminate\Contracts\Foundation\Application
    {
        return view('samsonapi::livewire.samson-api-time.samson-api-time-index');
    }
}
