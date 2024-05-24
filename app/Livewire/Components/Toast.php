<?php

namespace App\Livewire\Components;

class Toast
{
    protected string $title;

    protected string $text;

    public function __construct(string $title, string $text)
    {
        $this->title = $title;
        $this->text = $text;
    }

    public function success()
    {
        return <<<JS
                new Toast({
                    title: '{$this->title}',
                    text: '{$this->text}',
                    theme: 'success',
                    autohide: true
                });
            JS;
    }

    public function danger()
    {
        return <<<JS
                new Toast({
                    title: '{$this->title}',
                    text: '{$this->text}',
                    theme: 'danger',
                    autohide: true
                });
            JS;
    }

    public function warning()
    {
        return <<<JS
                new Toast({
                    title: '{$this->title}',
                    text: '{$this->text}',
                    theme: 'warning',
                    autohide: true
                });
            JS;
    }

    public function info()
    {
        return <<<JS
                new Toast({
                    title: '{$this->title}',
                    text: '{$this->text}',
                    theme: 'info',
                    autohide: true
                });
            JS;
    }
}
