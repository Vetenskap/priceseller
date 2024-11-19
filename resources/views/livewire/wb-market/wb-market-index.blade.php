<div>
    <x-layouts.header name="ВБ"/>

    @if($this->user()->can('create-wb'))
        <flux:modal name="create-wb-market" class="md:w-96 space-y-6">
            <div>
                <flux:heading size="lg">Создание кабинета</flux:heading>
            </div>

            <flux:input wire:model="form.name" label="Наименование" required badge="обязательное"/>
            <flux:input wire:model="form.api_key" label="АПИ ключ" required badge="обязательное"/>

            <div class="flex">
                <flux:spacer/>

                <flux:button variant="primary" wire:click="store">Создать</flux:button>
            </div>
        </flux:modal>

        <x-layouts.actions>
            <flux:modal.trigger name="create-wb-market">
                <flux:button>Добавить</flux:button>
            </flux:modal.trigger>
        </x-layouts.actions>
    @endif

    <x-layouts.main-container>
        <x-blocks.main-block>
            <flux:heading size="xl">Список</flux:heading>
        </x-blocks.main-block>
        <x-blocks.main-block>
            @if($this->markets->count() > 0)
                <flux:table :paginate="$this->markets">
                    <flux:columns>
                        <flux:column sortable :sorted="$sortBy === 'name'" :direction="$sortDirection"
                                     wire:click="sort('name')">Кабинет
                        </flux:column>
                        <flux:column sortable :sorted="$sortBy === 'organizations.name'" :direction="$sortDirection"
                                     wire:click="sort('organizations.name')">Организация
                        </flux:column>
                        <flux:column sortable :sorted="$sortBy === 'updated_at'" :direction="$sortDirection"
                                     wire:click="sort('updated_at')">Последнее обновление
                        </flux:column>
                    </flux:columns>

                    <flux:rows>
                        @foreach ($this->markets as $market)
                            <flux:row :key="$market->id">
                                <flux:cell class="flex items-center gap-3">
                                    {{ $market->name }}
                                </flux:cell>

                                <flux:cell>
                                    {{ $market->organization?->name }}
                                </flux:cell>

                                <flux:cell variant="strong">{{ $market->updated_at }}</flux:cell>

                                <flux:cell align="right">
                                    <flux:switch :checked="$market->open" disabled/>
                                </flux:cell>

                                <flux:cell align="right">
                                    <flux:button icon="pencil-square" size="sm"
                                                 :href="route('wb-market-edit', ['market' => $market->getKey()])"/>
                                </flux:cell>

                                @if($this->user()->can('delete-wb'))
                                    <flux:cell align="right">
                                        <flux:button icon="trash" variant="danger" size="sm"
                                                     wire:click="destroy({{ json_encode($market->getKey()) }})"
                                                     wire:target="destroy({{ json_encode($market->getKey()) }})"
                                                     wire:confirm="Вы действительно хотите удалить кабинет? Все связи так же будут удалены."/>
                                    </flux:cell>
                                @endif

                            </flux:row>
                        @endforeach
                    </flux:rows>
                </flux:table>
            @else
                <flux:subheading>Сейчас у вас нет кабинетов ВБ</flux:subheading>
            @endif
        </x-blocks.main-block>
    </x-layouts.main-container>
</div>
