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
        @if($this->user()->can('update-ozon'))
            <flux:button wire:click="update">Сохранить</flux:button>
        @endif
        @if($this->user()->can('delete-ozon'))
            <flux:button variant="danger" wire:click="destroy"
                         wire:confirm="Вы действительно хотите удалить кабинет? Все связи так же будут удалены.">Удалить
            </flux:button>
        @endif

    </x-layouts.actions>
    <x-layouts.main-container>

        <flux:tab.group>
            <x-blocks.main-block>
                <flux:tabs>
                    <flux:tab name="general" icon="home">Основные</flux:tab>
                    <flux:tab name="prices" icon="currency-dollar">Цены</flux:tab>
                    <flux:tab name="relationships_commissions" icon="link">Связи и комиссии</flux:tab>
                    <flux:tab name="stocks_warehouses" icon="truck">Остатки и склады</flux:tab>
                    <flux:tab name="export" icon="arrow-up-tray">Экспорт</flux:tab>
                    <flux:tab name="actions" icon="plus-circle">Действия</flux:tab>
                </flux:tabs>
            </x-blocks.main-block>

            <flux:tab.panel name="general">
                <x-blocks.main-block>
                    <flux:card class="space-y-6">
                        <flux:switch wire:model.live="form.open" label="Включить"/>
                        <div class="flex gap-12">
                            <flux:input wire:model.live="form.name" label="Наименование" required/>
                            <flux:input wire:model.live="form.client_id" label="Идентификатор клиента" required/>
                            <flux:input wire:model.live="form.api_key" label="АПИ ключ" required/>
                            <flux:select variant="combobox" placeholder="Выберите опцию..."
                                         label="Организация" wire:model.live="form.organization_id">

                                @foreach($this->currentUser()->organizations as $organization)
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
                        <flux:select wire:model.live="form.tariff" placeholder="Выберите схему работы..." label="Схема работы">
                            <flux:option value="fbs">FBS</flux:option>
                            <flux:option value="fbo">FBO</flux:option>
                        </flux:select>
                        <div class="flex">
                            <div class="flex gap-6">
                                <flux:switch wire:model.live="form.seller_price" label="Учитывать цену конкурента"/>
                                <flux:switch wire:model.live="form.enabled_price" label="Выгружать цены"/>
                            </div>
                        </div>
                        <div class="flex gap-6 flex-wrap">
                            <div>
                                <flux:field>
                                    <flux:tooltip content="Итоговая минимальная цена умноженная на этот коэффициент">
                                        <flux:label>Коэффициент увел. мин. цены</flux:label>
                                    </flux:tooltip>

                                    <flux:input wire:model.live="form.min_price_coefficient" type="number"/>

                                    <flux:error name="form.min_price_coefficient"/>
                                </flux:field>
                            </div>
                            <div>
                                <flux:field>
                                    <flux:tooltip content="Минимальная цена * %">
                                        <flux:label>Цена до скидки, %</flux:label>
                                    </flux:tooltip>

                                    <flux:input wire:model.live="form.max_price_percent" type="number"/>

                                    <flux:error name="form.max_price_percent"/>
                                </flux:field>
                            </div>
                            <div>
                                <flux:field>
                                    <flux:tooltip content="Минимальная цена * %">
                                        <flux:label>Цена продажи, %</flux:label>
                                    </flux:tooltip>

                                    <flux:input wire:model.live="form.seller_price_percent" type="number"/>

                                    <flux:error name="form.seller_price_percent"/>
                                </flux:field>
                            </div>
                            <div>
                                <flux:field>
                                    <flux:tooltip content="">
                                        <flux:label>Эквайринг</flux:label>
                                    </flux:tooltip>

                                    <flux:input wire:model.live="form.acquiring" type="number"/>

                                    <flux:error name="form.acquiring"/>
                                </flux:field>
                            </div>
                            <div>
                                <flux:field>
                                    <flux:tooltip content="Считается 5,5 % от цены на сайте">
                                        <flux:label>Последняя миля</flux:label>
                                    </flux:tooltip>

                                    <flux:input wire:model.live="form.last_mile" type="number"/>

                                    <flux:error name="form.last_mile"/>
                                </flux:field>
                            </div>
                            <div>
                                <flux:field>
                                    <flux:tooltip content="Берет комиссию мили не выше этой">
                                        <flux:label>Максимальная миля</flux:label>
                                    </flux:tooltip>

                                    <flux:input wire:model.live="form.max_mile" type="number"/>

                                    <flux:error name="form.max_mile"/>
                                </flux:field>
                            </div>
                        </div>
                    </flux:card>
                </x-blocks.main-block>
            </flux:tab.panel>
            <flux:tab.panel name="stocks_warehouses">
                <x-marketPages.stocks-warehouses :market="$market"/>
                <livewire:ozon-warehouse.ozon-warehouse-index :market="$market" />
            </flux:tab.panel>
            <flux:tab.panel name="relationships_commissions">
                <x-marketPages.relationships-commissions :items="$this->items" :market="$market" :file="$file"
                                                         sort-by="$sortBy" sort-direction="$sortDirection">
                    <div>
                        <flux:field>
                            <flux:tooltip content="Какой процент добавить к цене закупки для получения чистой прибыли">
                                <flux:label>Минимальная цена, %</flux:label>
                            </flux:tooltip>

                            <flux:input wire:model.live="form.min_price_percent_comm" type="number"/>

                            <flux:error name="form.min_price_percent_comm"/>
                        </flux:field>
                    </div>
                    <div>
                        <flux:field>
                            <flux:tooltip content="Ниже этой цены в маркет не выгрузится">
                                <flux:label>Минимальная цена продажи</flux:label>
                            </flux:tooltip>

                            <flux:input wire:model.live="form.min_price" type="number"/>

                            <flux:error name="form.min_price"/>
                        </flux:field>
                    </div>
                    <div>
                        <flux:field>
                            <flux:tooltip content="">
                                <flux:label>Обработка отправления</flux:label>
                            </flux:tooltip>

                            <flux:input wire:model.live="form.shipping_processing" type="number"/>

                            <flux:error name="form.shipping_processing"/>
                        </flux:field>
                    </div>
                </x-marketPages.relationships-commissions>
            </flux:tab.panel>
            <flux:tab.panel name="export">
                <x-marketPages.export :market="$market"/>
            </flux:tab.panel>
            <flux:tab.panel name="actions">
                <x-marketPages.actions :market="$market"/>
            </flux:tab.panel>
        </flux:tab-group>
    </x-layouts.main-container>
    @if($this->user()->can('update-ozon'))
        {!! $this->renderSaveButton() !!}
    @endif
</div>
