<div>
    <x-layouts.header name="Почта"/>

    <flux:modal name="create-email" class="md:w-96 space-y-6">
        <div>
            <flux:heading size="lg">Создание почты</flux:heading>
        </div>

        <flux:input wire:model="form.name" label="Наименование" badge="обязательное" required/>
        <flux:input wire:model="form.address" label="Адрес" badge="обязательное" type="email" required/>
        <flux:input wire:model="form.password" label="Адрес" badge="обязательное" type="password" required/>

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
                        <flux:column sortable :sorted="$sortBy === 'updated_at'" :direction="$sortDirection"
                                     wire:click="sort('updated_at')">Последнее обновление
                        </flux:column>
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
                                    <flux:button :href="route('email.edit', ['email' => $email->getKey()])" icon="pencil-square" size="sm" />
                                </flux:cell>

                                <flux:cell align="right">
                                    <flux:button icon="trash" variant="danger" size="sm"
                                                 wire:click="destroy({{ json_encode($email->getKey()) }})"
                                                 wire:target="destroy({{ json_encode($email->getKey()) }})"
                                                 wire:confirm="Вы действительно хотите удалить эту почту? Это действие нельзя будет отменить."/>
                                </flux:cell>

                            </flux:row>
                        @endforeach
                    </flux:rows>
                </flux:table>
            @else
                <flux:subheading>Сейчас у вас нет почты</flux:subheading>
            @endif
        </x-blocks.main-block>
    </x-layouts.main-container>
</div>
