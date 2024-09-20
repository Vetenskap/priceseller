<?php

namespace Modules\EditorContent\Livewire\EditorContent;

use App\Livewire\ModuleComponent;
use Livewire\Component;
use Livewire\WithFileUploads;

class EditorContentIndex extends ModuleComponent
{
    use WithFileUploads;

    public $article;
    public $cards = [];
    public $selectedCard;

    public $name;

    public $description;

    public $images = [];

    public function save()
    {
        // Логика для сохранения изображений
        $this->validate([
            'images.*' => 'image|max:1024', // Максимальный размер 1MB на изображение
        ]);

        foreach ($this->images as $image) {
            // Сохранить каждое изображение в папку storage/app/public/photos
            $image->store('photos', 'public');
        }

        // Логика для сохранения информации о карточке...

        session()->flash('message', 'Карточки успешно обновлены.');
    }

    // Метод для поиска карточек по API
    public function search()
    {
        // Запрос к API маркетплейсов
        $this->cards = $this->fetchCards($this->article);
    }

    // Метод для выбора карточки
    public function selectCard($cardId)
    {
        $this->selectedCard = collect($this->cards)->firstWhere('id', $cardId);
    }



    // Пример метода для получения карточек с маркетплейсов
    private function fetchCards($article)
    {
        // Реализация запроса к API и возврат данных
        return [
            ['id' => 1, 'image' => 'https://autodizel.expert/images/tild3438-3630-4034-a230-663336303663__purepngcom-engine-mo.png', 'account' => 'ОЗОН (Акс)', 'article' => $article],
            ['id' => 2, 'image' => 'https://www.feather-diesel.co.uk/wp-content/uploads/2017/02/34_FD_May12-1.jpg', 'account' => 'ВБ (Скад)', 'article' => $article],
        ];
    }

    public function render()
    {
        return view('editorcontent::livewire.editor-content.editor-content-index', [
            'modules' => $this->getEnabledModules()
        ]);
    }
}
