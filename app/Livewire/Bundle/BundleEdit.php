<?php

namespace App\Livewire\Bundle;

use App\Livewire\BaseComponent;
use App\Livewire\Forms\Bundle\BundlePostForm;
use App\Models\Bundle;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Livewire\Component;

class BundleEdit extends BaseComponent
{
    public BundlePostForm $form;

    public Bundle $bundle;

    public function update(): void
    {
        $this->authorizeForUser($this->user(), 'update', $this->bundle);

        $this->form->update();

        $this->addSuccessSaveNotification();
    }

    public function destroy(): void
    {
        $this->authorizeForUser($this->user(), 'delete', $this->bundle);

        $this->form->destroy();

        $this->redirectRoute('bundles.index');
    }

    public function mount(): void
    {
        $this->form->setBundle($this->bundle);
    }

    public function render(): Factory|Application|View|\Illuminate\View\View|\Illuminate\Contracts\Foundation\Application
    {
        $this->authorizeForUser($this->user(), 'view', $this->bundle);

        return view('livewire.bundle.bundle-edit');
    }
}
