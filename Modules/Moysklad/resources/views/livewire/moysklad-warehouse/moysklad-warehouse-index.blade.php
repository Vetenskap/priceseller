<div>
    <flux:modal name="create-moysklad-warehouse" class="md:w-96 space-y-6">
        <div>
            <flux:heading size="lg">Добавление склада</flux:heading>
        </div>

        <flux:select variant="combobox" placeholder="Выберите склад..." label="Ваши склады (priceseller)"
                     wire:model="form.warehouse_id">

            @foreach(auth()->user()->warehouses as $warehouse)
                <flux:option :value="$warehouse->getKey()">{{$warehouse->name}}</flux:option>
            @endforeach
        </flux:select>

        <flux:select variant="combobox" placeholder="Выберите склад..." label="Ваши склады (Мой склад)"
                     wire:model="form.moysklad_warehouse_uuid">

            @foreach($moyskladWarehouses as $warehouse)
                <flux:option :value="$warehouse['id']">{{$warehouse['name']}}</flux:option>
            @endforeach
        </flux:select>

        <div class="flex">
            <flux:spacer/>

            <flux:button variant="primary" wire:click="store">Создать</flux:button>
        </div>
    </flux:modal>

    <x-blocks.main-block>
        <flux:card class="space-y-6">
            <flux:heading size="xl">Добавление нового склада</flux:heading>
            <flux:subheading>Вы можете привязать свои склады с Моего Склада к своим существующим.</flux:subheading>
            <div>
                <flux:modal.trigger name="create-moysklad-warehouse">
                    <flux:button>Добавить</flux:button>
                </flux:modal.trigger>
            </div>
        </flux:card>
    </x-blocks.main-block>
    <x-blocks.main-block>

        <flux:card class="space-y-6">
            <flux:heading size="xl">Список</flux:heading>
            @if($this->warehouses->isNotEmpty())
                <flux:table :paginate="$this->warehouses">
                    <flux:columns>
                        <flux:column>Склад priceseller</flux:column>
                        <flux:column>Склад мой склад</flux:column>
                    </flux:columns>
                    <flux:rows>
                        @foreach($this->warehouses as $warehouse)
                            <flux:row :key="$warehouse->getKey()">
                                <flux:cell>{{collect($moyskladWarehouses)->firstWhere('id', $warehouse->moysklad_warehouse_uuid)['name']}}</flux:cell>
                                <flux:cell>{{$warehouse->warehouse->name}}</flux:cell>
                                <flux:cell align="right">
                                    <flux:icon.trash wire:click="destroy({{ json_encode($warehouse->getKey()) }})"
                                                     wire:loading.remove
                                                     wire:target="destroy({{ json_encode($warehouse->getKey()) }})"
                                                     wire:confirm="Вы действительно хотите удалить этот склад?"
                                                     class="cursor-pointer hover:text-red-400"/>
                                    <flux:icon.loading wire:loading
                                                       wire:target="destroy({{ json_encode($warehouse->getKey()) }})"/>
                                </flux:cell>
                                <flux:cell align="right">
                                    <flux:tooltip content="Выгрузить все остатки">
                                        <flux:button icon="arrow-up-tray"
                                                     wire:click="updateStocks({{ json_encode($warehouse->getKey()) }})"/>
                                    </flux:tooltip>
                                </flux:cell>
                            </flux:row>
                        @endforeach
                    </flux:rows>
                </flux:table>
            @endif
        </flux:card>
    </x-blocks.main-block>
    <x-blocks.main-block>

        <flux:card class="space-y-6">
            <flux:heading size="xl">Вебхук</flux:heading>
            @if($webhook = $moysklad->webhooks()->where(['action' => 'UPDATE', 'type' => 'warehouses'])->first())
                <flux:subheading>Дата создания: {{$webhook->created_at}}</flux:subheading>
                <flux:button variant="danger" wire:click="deleteWebhook">Удалить</flux:button>
            @else
                <flux:button wire:click="addWebhook">Добавить</flux:button>
            @endif
        </flux:card>
    </x-blocks.main-block>
</div>
