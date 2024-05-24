<?php

namespace App\Livewire\Forms;

use App\Models\EmailSupplier;
use Livewire\Attributes\Validate;
use Livewire\Form;

class EmailSupplierPostForm extends Form
{
    public EmailSupplier $emailSupplier;

    #[Validate]
    public $email;

    #[Validate]
    public $filename;

    #[Validate]
    public $header_article;

    #[Validate]
    public $header_brand;

    public $header_price;
    public $header_count;

    public function setEmailSupplier(EmailSupplier $emailSupplier)
    {
        $this->email = $emailSupplier->email;
        $this->filename = $emailSupplier->filename;
        $this->header_article = $emailSupplier->header_article;
        $this->header_brand = $emailSupplier->header_brand;
        $this->header_price = $emailSupplier->header_price;
        $this->header_count = $emailSupplier->header_count;
        $this->emailSupplier = $emailSupplier;
    }

    public function rules()
    {
        return [
            'email' => ['required', 'email'],
            'filename' => ['required', 'min:3'],
        ];
    }

//    public function messages()
//    {
//        return [
//            'name' => 'Поле должно быть не меньше 5 значений',
//        ];
//    }

//    public function store()
//    {
//        $this->validate();
//
//        $email = Email::create(Arr::add($this->except('email'), 'user_id', \auth()->user()->id));
//        $email->refresh();
//
//        $this->reset();
//
//        return $email;
//
//    }

    public function update()
    {
        $this->validate();

        $this->emailSupplier->update($this->except('emailSupplier'));

    }
}
