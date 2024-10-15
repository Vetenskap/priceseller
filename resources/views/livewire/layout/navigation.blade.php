<?php

use App\Livewire\Actions\Logout;
use Livewire\Volt\Component;

new class extends Component {
    /**
     * Log the current user out of the application.
     */
    public function logout(Logout $logout): void
    {
        $logout();

        $this->redirect('/', navigate: true);
    }
}; ?>

<nav x-data="{ open: false }" class="bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" wire:navigate>
                        <x-application-logo class="block h-9 w-auto fill-current text-gray-800 dark:text-gray-200"/>
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')"
                                wire:navigate.hover>
                        {{ __('Главная') }}
                    </x-nav-link>
                    {{--                    <x-nav-link :href="route('moysklad')" :active="request()->routeIs('moysklad')" wire:navigate.hover>--}}
                    {{--                        {{ __('Мой склад') }}--}}
                    {{--                    </x-nav-link>--}}
                    {{--                    <x-nav-link :href="route('avito')" :active="request()->routeIs('avito')" wire:navigate.hover>--}}
                    {{--                        {{ __('Авито') }}--}}
                    {{--                    </x-nav-link>--}}
                    <x-nav-link :href="route('emails.index')" :active="request()->routeIs('emails.index', 'email.edit')"
                                wire:navigate.hover>
                        {{ __('Почта') }}
                    </x-nav-link>
                    <x-nav-link :href="route('suppliers.index')" :active="request()->routeIs('suppliers.index', 'suppliers.edit')"
                                wire:navigate.hover>
                        {{ __('Поставщики') }}
                    </x-nav-link>
                    <x-nav-link :href="route('items')" :active="request()->routeIs('items', 'item-edit')"
                                wire:navigate.hover>
                        {{ __('Товары') }}
                    </x-nav-link>
                    <x-nav-link :href="route('bundles.index')" :active="request()->routeIs('bundles.index', 'bundles.edit')"
                                wire:navigate.hover>
                        {{ __('Комплекты') }}
                    </x-nav-link>
                    <x-nav-link :href="route('ozon')" :active="request()->routeIs('ozon', 'ozon-market-edit')"
                                wire:navigate.hover>
                        {{ __('ОЗОН') }}
                    </x-nav-link>
                    <x-nav-link :href="route('wb')" :active="request()->routeIs('wb', 'wb-market-edit')"
                                wire:navigate.hover>
                        {{ __('ВБ') }}
                    </x-nav-link>
                    <x-nav-link :href="route('organizations.index')" :active="request()->routeIs('organizations.index')"
                                wire:navigate.hover>
                        {{ __('Организации') }}
                    </x-nav-link>
                    <x-nav-link :href="route('warehouses.index')"
                                :active="request()->routeIs('warehouses.index', 'warehouses.edit')" wire:navigate.hover>
                        {{ __('Склады') }}
                    </x-nav-link>
                    <x-nav-link :href="route('modules.index')" :active="str_contains(request()->getUri(), 'modules')"
                                wire:navigate.hover>
                        {{ __('Модули') }}
                    </x-nav-link>
                    <x-nav-link :href="route('base-settings.index')" :active="request()->routeIs('base-settings.index')"
                                wire:navigate.hover>
                        {{ __('Общие настройки') }}
                    </x-nav-link>
                </div>
            </div>

            {{--            <div class="flex sm:items-center">--}}
            {{--                <i class="fa-regular fa-bell fa-lg cursor-pointer"></i>--}}
            {{--            </div>--}}
            {{--            <div class="bg-red-300 rounded position-absolute w-72 h-72 flex justify-center top-14">--}}
            {{--                <h1>Hello</h1>--}}
            {{--            </div>--}}

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button
                            class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none transition ease-in-out duration-150">
                            <div x-data="{{ json_encode(['name' => session()->has('employee_id') ? \App\Models\Employee::find(session('employee_id'))->name : auth()->user()->name]) }}" x-text="name"
                                 x-on:profile-updated.window="name = $event.detail.name"></div>

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg"
                                     viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                          d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                          clip-rule="evenodd"/>
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        @if(!session()->has('employee_id'))
                            <x-dropdown-link :href="route('profile')" wire:navigate>
                                {{ __('Профиль') }}
                            </x-dropdown-link>
                        @endif

                        <!-- Authentication -->
                        <button wire:click="logout" class="w-full text-start">
                            <x-dropdown-link>
                                {{ __('Выйти') }}
                            </x-dropdown-link>
                        </button>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open"
                        class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 dark:text-gray-500 hover:text-gray-500 dark:hover:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-900 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-900 focus:text-gray-500 dark:focus:text-gray-400 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex"
                              stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 6h16M4 12h16M4 18h16"/>
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round"
                              stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>


    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" wire:navigate>
                {{ __('Главная') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('emails.index')" :active="request()->routeIs('emails.index', 'email.edit')"
                                   wire:navigate.hover>
                {{ __('Почта') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('suppliers.index')" :active="request()->routeIs('suppliers.index', 'suppliers.edit')"
                                   wire:navigate.hover>
                {{ __('Поставщики') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('items')" :active="request()->routeIs('items', 'item-edit')"
                                   wire:navigate.hover>
                {{ __('Товары') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('bundles.index')" :active="request()->routeIs('bundles.index', 'bundles.edit')"
                                   wire:navigate.hover>
                {{ __('Комплекты') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('ozon')" :active="request()->routeIs('ozon', 'ozon-market-edit')"
                                   wire:navigate.hover>
                {{ __('ОЗОН') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('wb')" :active="request()->routeIs('wb', 'wb-market-edit')"
                                   wire:navigate.hover>
                {{ __('ВБ') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('organizations.index')"
                                   :active="request()->routeIs('organizations.index')" wire:navigate.hover>
                {{ __('Организации') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('warehouses.index')"
                                   :active="request()->routeIs('warehouses.index', 'warehouses.edit')"
                                   wire:navigate.hover>
                {{ __('Склады') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('modules.index')" :active="str_contains(request()->getUri(), 'modules')"
                                   wire:navigate.hover>
                {{ __('Модули') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('base-settings.index')" :active="request()->routeIs('base-settings.index')"
                                   wire:navigate.hover>
                {{ __('Общие настройки') }}
            </x-responsive-nav-link>
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200 dark:border-gray-600">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800 dark:text-gray-200"
                     x-data="{{ json_encode(['name' => auth()->user()->name]) }}" x-text="name"
                     x-on:profile-updated.window="name = $event.detail.name"></div>
                <div class="font-medium text-sm text-gray-500">{{ auth()->user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile')" wire:navigate>
                    {{ __('Профиль') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <button wire:click="logout" class="w-full text-start">
                    <x-responsive-nav-link>
                        {{ __('Выйти') }}
                    </x-responsive-nav-link>
                </button>
            </div>
        </div>
    </div>
</nav>
