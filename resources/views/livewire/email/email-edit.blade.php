<div>
    <x-layouts.header :name="$email->name"/>

    <x-layouts.actions>
        <flux:button variant="primary" wire:click="back">Закрыть</flux:button>
        <flux:button wire:click="update">Сохранить</flux:button>
        <flux:modal.trigger name="delete-email">
            <flux:button variant="danger">Удалить</flux:button>
        </flux:modal.trigger>

        <flux:modal name="delete-email" class="min-w-[22rem] space-y-6">
            <div>
                <flux:heading size="lg">Удалить почту?</flux:heading>

                <flux:subheading>
                    <p>Вы действительно хотите удалить эту почту?</p>
                    <p>Это действие нельзя будет отменить.</p>
                </flux:subheading>
            </div>

            <div class="flex gap-2">
                <flux:spacer />

                <flux:modal.close>
                    <flux:button variant="ghost">Отменить</flux:button>
                </flux:modal.close>

                <flux:button wire:click="destroy" variant="danger">Удалить</flux:button>
            </div>
        </flux:modal>
    </x-layouts.actions>

    <x-layouts.main-container>
        <x-blocks.main-block>
            <flux:heading size="xl">Основная информация</flux:heading>
        </x-blocks.main-block>
        <x-blocks.main-block class="max-w-fit">
            <flux:switch wire:model="form.open" label="Включен" />
        </x-blocks.main-block>
        <div class="flex p-6 gap-6">
            <flux:input wire:model="form.name" label="Наименование" required />
            <flux:input wire:model="form.address" label="Адрес" type="email" required />
            <flux:input wire:model="form.password" label="Пароль" type="password" required />
        </div>
    </x-layouts.main-container>
    <livewire:email-supplier.email-supplier-index :email="$email"/>
</div>

