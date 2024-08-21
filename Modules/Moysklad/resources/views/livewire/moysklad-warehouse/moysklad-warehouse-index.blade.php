<div>
    <x-layouts.main-container>
        <x-blocks.main-block>
            <x-layouts.title name="Добавление нового склада" />
            <x-information>
                Вы можете привязать свои склады с Моего Склада к своим существующим.
            </x-information>
        </x-blocks.main-block>
        <div x-data="{ open: false }">
            <x-blocks.main-block>
                <x-secondary-button @click="open = ! open">Добавить</x-secondary-button>
            </x-blocks.main-block>
            <div x-show="open">
                <x-blocks.flex-block>
                    <x-dropdowns.dropdown-select name="warehouse_id"
                                                 :items="auth()->user()->warehouses"
                                                 :current-id="$form->warehouse_id"
                                                 field="form.warehouse_id"
                    >
                        Ваши склады (priceseller)
                    </x-dropdowns.dropdown-select>
                    <x-dropdowns.dropdown-select name="moysklad_warehouse_id"
                                                 :items="$moyskladWarehouses"
                                                 :current-id="$form->moysklad_warehouse_uuid"
                                                 field="form.moysklad_warehouse_uuid"
                    >
                        Ваши склады (Мой склад)
                    </x-dropdowns.dropdown-select>
                </x-blocks.flex-block>
                <x-blocks.main-block>
                    <x-success-button wire:click="store">Добавить</x-success-button>
                </x-blocks.main-block>
            </div>
        </div>
    </x-layouts.main-container>
    @if($moysklad->warehouses->isNotEmpty())
        <x-layouts.main-container>
            <x-blocks.main-block>
                <x-layouts.title name="Список" />
            </x-blocks.main-block>
            <x-blocks.main-block>
                <x-success-button wire:click="update">Сохранить</x-success-button>
            </x-blocks.main-block>
            @foreach($moysklad->warehouses as $warehouse)
                <livewire:moysklad::moysklad-warehouse.moysklad-warehouse-edit :warehouse="$warehouse"
                                                                               wire:key="{{$warehouse->id}}"
                                                                               :moysklad="$moysklad"/>
            @endforeach
        </x-layouts.main-container>
    @endif
    <x-layouts.main-container>
        <x-blocks.main-block>
            <x-layouts.title name="Вебхук"/>
        </x-blocks.main-block>
        <x-blocks.main-block>
            @if($webhook = $moysklad->webhooks()->where(['action' => 'UPDATE', 'type' => 'warehouses'])->first())
                <x-information>Дата создания: {{$webhook->created_at}}</x-information>
                <x-danger-button wire:click="deleteWebhook">Удалить</x-danger-button>
            @else
                <x-success-button wire:click="addWebhook">Добавить</x-success-button>
            @endif
        </x-blocks.main-block>
    </x-layouts.main-container>
    <div wire:loading wire:target="deleteWebhook, addWebhook">
        <x-loader/>
    </div>
</div>
