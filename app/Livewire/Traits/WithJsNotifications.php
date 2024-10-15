<?php

namespace App\Livewire\Traits;

use App\Livewire\Components\Toast;
use Livewire\Attributes\On;

trait WithJsNotifications
{
    #[On('livewire-upload-error')]
    public function err()
    {
        $this->js((new Toast('Ошибка', "Не удалось загрузить файл"))->danger());
    }

    public function addJobNotification()
    {
        \Flux::toast('Задача добавлена, пожалуйста, ожидайте..');
    }

    public function addJobFailedNotification()
    {
        $this->js((new Toast('Ошибка', 'Задача уже добавлена, пожалуйста, ожидайте..'))->info());
    }

    public function addJobAlready()
    {
        $this->js((new Toast('Ошибка', 'Задача уже находится в обработке, ожидайте..'))->danger());
    }

    public function addDeleteNotification()
    {
        $this->js((new Toast('Уведомление', 'Успешно удалено'))->info());
    }

    public function addWarningImportNotification()
    {
        $this->js((new Toast('Не правомерное действие', "Вы не можете добавить эту задачу, пока не завершился импорт"))->warning());
    }

    public function addSuccessClearRelationshipsNotification(int $deleted)
    {
        $this->js((new Toast('Уведомление', "{$deleted} связей успешно удалено"))->info());
    }

    public function addSuccessSaveNotification()
    {
        \Flux::toast('Сохранено');
    }

    public function addSuccessDeleteNotification()
    {
        \Flux::toast('Удалено');
    }

    public function addSuccessTestPriceNotification()
    {
        \Flux::toast('Все цены перерасчитаны');
    }

    public function addSuccessTestStocksPriceNotification()
    {
        \Flux::toast('Все остатки перерасчитаны');
    }

    public function addSuccessNullStocksNotification()
    {
        \Flux::toast('Все остатки занулены');
    }
}
