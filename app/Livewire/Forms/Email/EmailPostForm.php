<?php

namespace App\Livewire\Forms\Email;

use App\Helpers\Helpers;
use App\Models\Email;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;
use Livewire\Attributes\Validate;
use Livewire\Form;

class EmailPostForm extends Form
{
    public ?Email $email = null;

    #[Validate]
    public $name;

    #[Validate]
    public $address;

    #[Validate]
    public $password;

    #[Validate]
    public $open = false;

    public function setEmail(Email $email): void
    {
        $this->email = $email;
        $this->name = $email->name;
        $this->address = $email->address;
        $this->password = $email->password;
        $this->open = $email->open;
    }

    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'min:5',
                'string',
                Rule::unique('emails', 'name')
                    ->where('user_id', Helpers::user()->id)
                    ->when($this->email, fn (Unique $unique) => $unique->ignore($this->email->id, 'id'))
            ],
            'address' => [
                'required',
                'email',
                Rule::unique('emails', 'address')
                    ->when($this->email, fn (Unique $unique) => $unique->ignore($this->email->id, 'id'))
            ],
            'password' => ['required', 'min:5'],
            'open' => ['nullable', 'boolean'],
        ];
    }

    public function store(): void
    {
        $this->validate();

        Helpers::user()->emails()->create($this->except('email'));

        $this->reset();

    }

    public function update(): void
    {
        $this->validate();

        $this->email->update($this->except('email'));

    }

    public function destroy(): void
    {
        $this->email->delete();
    }
}
