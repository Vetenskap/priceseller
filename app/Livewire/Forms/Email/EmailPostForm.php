<?php

namespace App\Livewire\Forms\Email;

use App\Models\Email;
use Illuminate\Support\Arr;
use Livewire\Attributes\Validate;
use Livewire\Form;

class EmailPostForm extends Form
{
    public ?Email $email;

    #[Validate]
    public $name;

    #[Validate]
    public $address;

    #[Validate]
    public $password;

    #[Validate]
    public $open = true;

    public $suppliers;

    public function setEmail(Email $email)
    {
        $this->email = $email;
        $this->name = $email->name;
        $this->address = $email->address;
        $this->password = $email->password;
        $this->open = $email->open;
        $this->suppliers = $email->suppliers;
    }

    public function rules()
    {
        return [
            'name' => ['required', 'min:5', 'string'],
            'address' => ['required', 'email'],
            'password' => ['required', 'min:5'],
            'open' => ['nullable', 'boolean'],
        ];
    }

    public function messages()
    {
        return [
            'name' => 'Поле должно быть не меньше 5 значений',
        ];
    }

    public function store()
    {
        $this->validate();

        $email = Email::create(Arr::add($this->except('email'), 'user_id', \auth()->user()->id));
        $email->refresh();

        $this->reset();

        return $email;

    }

    public function update()
    {
        $this->validate();

        $this->email->update($this->except('email'));

    }
}
