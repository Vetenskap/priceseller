<?php

use App\Livewire\Actions\Logout;
use Illuminate\Support\Facades\Auth;
use Livewire\Volt\Component;
use Illuminate\Validation\Rules;
use App\Models\Employee;
use Illuminate\Support\Facades\Hash;

new class extends Component {
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    /**
     * Handle an incoming registration request.
     */
    public function register(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . Employee::class],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ]);

        $validated['password'] = Hash::make($validated['password']);

        $employee = \auth()->user()->employees()->create($validated);

    }

    public function destroy($id): void
    {
        $employee = Employee::find($id);

        // TODO: add authorization
//        $this->authorize('delete', $employee);

        $employee->delete();
    }

}; ?>

<section class="space-y-6">
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            {{ __('Сотрудники') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __('Вы можете добавить сотрудников, которые смогут просматрировать ваши данные.') }}
        </p>
    </header>

    <form wire:submit="register">
        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Имя')"/>
            <x-text-input wire:model="name" id="name" class="block mt-1 w-full" type="text" name="name" required
                          autofocus autocomplete="name"/>
            <x-input-error :messages="$errors->get('name')" class="mt-2"/>
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Почта')"/>
            <x-text-input wire:model="email" id="email" class="block mt-1 w-full" type="email" name="email" required
                          autocomplete="username"/>
            <x-input-error :messages="$errors->get('email')" class="mt-2"/>
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Пароль')"/>

            <x-text-input wire:model="password" id="password" class="block mt-1 w-full"
                          type="password"
                          name="password"
                          required autocomplete="new-password"/>

            <x-input-error :messages="$errors->get('password')" class="mt-2"/>
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Подтверждение пароля')"/>

            <x-text-input wire:model="password_confirmation" id="password_confirmation" class="block mt-1 w-full"
                          type="password"
                          name="password_confirmation" required autocomplete="new-password"/>

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2"/>
        </div>

        <div class="mt-4">
            <flux:button type="confim">Добавить</flux:button>
        </div>

        <div class="mt-4">
            <flux:card class="space-y-6">
                <flux:heading size="xl">Список</flux:heading>
                <flux:table>
                    <flux:columns>
                        <flux:column>Имя</flux:column>
                        <flux:column>Почта</flux:column>
                    </flux:columns>
                    <flux:rows>
                        @foreach(\auth()->user()->employees as $employee)
                            <flux:row :key="$employee->getKey()">
                                <flux:cell>{{$employee->name}}</flux:cell>
                                <flux:cell>{{$employee->email}}</flux:cell>
                                <flux:cell>
                                    <flux:button icon="trash" wire:click="destroy({{$employee->getKey()}})"/>
                                </flux:cell>
                            </flux:row>
                        @endforeach
                    </flux:rows>
                </flux:table>
            </flux:card>
        </div>
    </form>
</section>
