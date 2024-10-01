<div>
    <x-layouts.header name="Почта"/>

    <flux:modal name="create-email" class="md:w-96 space-y-6">
        <div>
            <flux:heading size="lg">Создание почты</flux:heading>
        </div>

        <flux:input wire:model="form.name" label="Наименование" required/>
        <flux:input wire:model="form.address" label="Адрес" type="email" required/>
        <flux:input wire:model="form.password" label="Адрес" type="password" required/>

        <div class="flex">
            <flux:spacer/>

            <flux:button variant="primary" wire:click="store">Создать</flux:button>
        </div>
    </flux:modal>

    <x-layouts.actions>
        <flux:modal.trigger name="create-email">
            <flux:button>Добавить</flux:button>
        </flux:modal.trigger>
    </x-layouts.actions>

    <x-layouts.main-container>
        <x-blocks.main-block>
            <flux:heading size="xl">Список</flux:heading>
        </x-blocks.main-block>
        <x-blocks.main-block>
            @if($this->emails->count() > 0)
                <flux:table :paginate="$this->emails">
                    <flux:columns>
                        <flux:column>Почта</flux:column>
                        <flux:column>Адрес</flux:column>
                        <flux:column sortable :sorted="$sortBy === 'updated_at'" :direction="$sortDirection" wire:click="sort('updated_at')">Последнее обновление</flux:column>
                    </flux:columns>

                    <flux:rows>
                        @foreach ($this->emails as $email)
                            <flux:row :key="$email->id">
                                <flux:cell class="flex items-center gap-3">
                                    {{ $email->name }}
                                </flux:cell>

                                <flux:cell class="whitespace-nowrap">{{ $email->address }}</flux:cell>

                                <flux:cell variant="strong">{{ $email->updated_at }}</flux:cell>

                                <flux:cell align="right">
                                    <flux:switch wire:model.live="dirtyEmails.{{ $email->id }}.open"/>
                                </flux:cell>

                                <flux:cell align="right">
                                    <flux:dropdown>
                                        <flux:button variant="ghost" size="sm" icon="ellipsis-horizontal" inset="top bottom"></flux:button>

                                        <flux:menu>
                                            <flux:menu.item icon="pencil-square" kbd="⌘S" wire:click="edit({{ json_encode($email->id) }})">Редактировать</flux:menu.item>
                                            <flux:menu.item icon="trash" variant="danger" kbd="⌘⌫" wire:click="destroy({{ json_encode($email->id) }})">Удалить</flux:menu.item>
                                        </flux:menu>
                                    </flux:dropdown>
                                </flux:cell>
                            </flux:row>
                        @endforeach
                    </flux:rows>
                </flux:table>
            @else
                <x-blocks.main-block>
                    <x-information>Сейчас у вас нет почты</x-information>
                </x-blocks.main-block>
            @endif
        </x-blocks.main-block>
    </x-layouts.main-container>
</div>
