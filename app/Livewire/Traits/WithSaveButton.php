<?php

namespace App\Livewire\Traits;

trait WithSaveButton
{
    public $showSaveButton = false;

    // Метод отслеживает изменения любого свойства в компоненте
    public function updated($propertyName)
    {
        // Показываем кнопку при любом изменении
        $this->showSaveButton = true;
    }

    // Метод для сброса состояния кнопки после сохранения
    public function hideSaveButton()
    {
        $this->showSaveButton = false;
    }

    // Метод для рендера кнопки в шаблоне
    public function renderSaveButton()
    {
        return view('components.save-button', ['show' => $this->showSaveButton]);
    }
}
