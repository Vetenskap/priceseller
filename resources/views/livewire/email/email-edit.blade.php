<div>
    <x-layouts.header :name="$email->name"/>

    <x-layouts.actions>
        <x-primary-button wire:click="back">Закрыть</x-primary-button>
        <x-success-button wire:click="update">Сохранить</x-success-button>
        <x-danger-button wire:click="destroy" wire:confirm="Вы действительно хотите удалить данную почту?">Удалить
        </x-danger-button>
    </x-layouts.actions>

    <x-layouts.main-container>
        <x-blocks.main-block>
            <x-layouts.title name="Основная информация"/>
        </x-blocks.main-block>
        <x-blocks.flex-block>
            <x-inputs.switcher :checked="$form->open" wire:model="form.open"/>
            <x-layouts.simple-text name="Включен"/>
        </x-blocks.flex-block>
        <x-blocks.flex-block>
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
        </x-blocks.flex-block>
    </x-layouts.main-container>
    <livewire:email-supplier.email-supplier-index :email="$email"/>
</div>

