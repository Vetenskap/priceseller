<div>
    <x-layouts.header :name="$email->name"/>

    <x-layouts.actions>
        <a href="{{url()->previous()}}" wire:navigate.hover>
            <x-primary-button>Закрыть</x-primary-button>
        </a>
        <x-success-button wire:click="save">Сохранить</x-success-button>
        <x-danger-button wire:click="destroy">Удалить</x-danger-button>
    </x-layouts.actions>

    <x-layouts.main-container wire:poll>
        <x-layouts.title name="Основная информация"/>
        <x-blocks.flex-block-end>
            <x-inputs.switcher :checked="$form->open" wire:model="form.open"/>
            <x-inputs.input-with-label name="name"
                                       type="text"
                                       field="form.name"
            >Наименование
            </x-inputs.input-with-label>
            <x-inputs.input-with-label name="address"
                                       type="email"
                                       field="form.address"
            >Адрес
            </x-inputs.input-with-label>
            <x-inputs.input-with-label name="password"
                                       type="email"
                                       field="form.password"
            >Пароль
            </x-inputs.input-with-label>
        </x-blocks.flex-block-end>
        <livewire:email-supplier.email-supplier-index :email="$email" />
    </x-layouts.main-container>
</div>

