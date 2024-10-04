<div>
    <x-layouts.header :name="$form->name"/>
    @error('error')
    <x-notify-top>
        <div class="bg-red-400 w-full p-2">
            <x-layouts.simple-text :name="$message"/>
        </div>
    </x-notify-top>
    @enderror
    <x-layouts.actions>
        <flux:button variant="primary" wire:click="back">Закрыть</flux:button>
        <flux:button wire:click="update">Сохранить</flux:button>
        <flux:button variant="danger" wire:click="destroy"
                     wire:confirm="Вы действительно хотите удалить кабинет? Все связи так же будут удалены.">Удалить
        </flux:button>
    </x-layouts.actions>
    <x-layouts.main-container>

        <flux:tab.group>
            <x-blocks.main-block>
                <flux:tabs>
                    <flux:tab name="general" icon="home">Основное</flux:tab>
                    <flux:tab name="prices" icon="currency-dollar">Цены</flux:tab>
                    <flux:tab name="stocks_warehouses" icon="truck">Остатки и склады</flux:tab>
                    <flux:tab name="relationships_commissions" icon="link">Связи и комиссии</flux:tab>
                    <flux:tab name="export" icon="arrow-up-tray">Экспорт</flux:tab>
                    <flux:tab name="actions" icon="plus-circle">Действия</flux:tab>
                </flux:tabs>
            </x-blocks.main-block>

            <flux:tab.panel name="general">
                <x-blocks.main-block>
                    <flux:card class="space-y-6">
                        <flux:switch wire:model="form.open" label="Включить"/>
                        <div class="flex gap-12">
                            <flux:input wire:model="form.name" label="Наименование" required/>
                            <flux:input wire:model="form.client_id" label="Идентификатор клиента" required/>
                            <flux:input wire:model="form.api_key" label="АПИ ключ" required/>
                            <flux:select variant="listbox" searchable placeholder="Выберите опцию..."
                                         label="Организация" wire:model="form.organization_id">
                                <x-slot name="search">
                                    <flux:select.search placeholder="Поиск..."/>
                                </x-slot>

                                @foreach(auth()->user()->organizations as $organization)
                                    <flux:option
                                        :value="$organization->getKey()">{{$organization->name}}</flux:option>
                                @endforeach
                            </flux:select>
                        </div>
                    </flux:card>
                </x-blocks.main-block>
            </flux:tab.panel>
            <flux:tab.panel name="prices">
                <x-blocks.main-block>
                    <flux:card class="space-y-12">
                        <div class="flex">
                            <div class="flex gap-6">
                                <flux:switch wire:model="form.seller_price" label="Учитывать цену конкурента"/>
                                <flux:switch wire:model="form.enabled_price" label="Выгружать цены"/>
                            </div>
                        </div>
                        <div class="flex gap-6 flex-wrap">
                            <div>
                                <flux:tooltip content="Итоговая минимальная цена умноженная на этот коэффициент">
                                    <flux:input wire:model="form.min_price_percent"
                                                label="Процент увел. мин. цены"/>
                                </flux:tooltip>
                            </div>
                            <div>
                                <flux:tooltip content="Минимальная цена * %">
                                    <flux:input wire:model="form.max_price_percent" label="Цена до скидки, %"/>
                                </flux:tooltip>
                            </div>
                            <div>
                                <flux:tooltip content="Минимальная цена * %">
                                    <flux:input wire:model="form.seller_price_percent" label="Цена продажи, %"/>
                                </flux:tooltip>
                            </div>
                            <flux:input wire:model="form.acquiring" label="Эквайринг"/>
                            <div>
                                <flux:tooltip content="Считается 5,5 % от цены на сайте">
                                    <flux:input wire:model="form.last_mile" label="Последняя миля"/>
                                </flux:tooltip>
                            </div>
                            <div>
                                <flux:tooltip content="Берет комиссию мили не выше этой">
                                    <flux:input wire:model="form.max_mile" label="Максимальная миля"/>
                                </flux:tooltip>
                            </div>
                        </div>
                    </flux:card>
                </x-blocks.main-block>
            </flux:tab.panel>
            <flux:tab.panel name="stocks_warehouses">
                <x-marketPages.stocks-warehouses :market="$market"/>
                <livewire:ozon-warehouse.ozon-warehouse-index :market="$market" :api-warehouses="$this->apiWarehouses"/>
            </flux:tab.panel>
            <flux:tab.panel name="relationships_commissions">
                <x-marketPages.relationships-commissions :items="$this->items" :market="$market" :file="$file" sort-by="$sortBy" sort-direction="$sortDirection">
                    <flux:tooltip content="Какой процент добавить к цене закупки для получения чистой прибыли">
                        <flux:input wire:model="min_price_percent" label="Минимальная цена, %" type="number"/>
                    </flux:tooltip>
                    <flux:tooltip content="Ниже этой цены в маркет не выгрузится">
                        <flux:input wire:model="min_price" label="Минимальная цена продажи" type="number"/>
                    </flux:tooltip>
                    <flux:input wire:model="shipping_processing" label="Обработка отправления" type="number"/>
                </x-marketPages.relationships-commissions>
            </flux:tab.panel>
            <flux:tab.panel name="export">
                <x-marketPages.export :market="$market"/>
            </flux:tab.panel>
            <flux:tab.panel name="actions">
                <x-marketPages.actions/>
            </flux:tab.panel>
        </flux:tab-group>
    </x-layouts.main-container>
</div>
