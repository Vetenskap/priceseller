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
        @if($this->user()->can('delete-wb'))
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
                        <div class="flex">
                            <flux:switch wire:model.live="form.enabled_price" label="Выгружать цены"/>
                        </div>
                        <flux:select wire:model.live="form.tariff" placeholder="Выберите схему работы..." label="Схема работы">
                            @foreach(\App\HttpClient\WbClient\Resources\Tariffs\Commission::TARRIFS as $tariff)
                                <flux:option :value="$tariff['name']">{{$tariff['label']}}</flux:option>
                            @endforeach
                        </flux:select>
                        <div class="flex gap-6 flex-wrap">
                            <flux:input wire:model.live="form.coefficient" label="Коэффициент" type="number"/>
                            <flux:input wire:model.live="form.basic_logistics" label="Базовая цена логистики"
                                        type="number"/>
                            <flux:input wire:model.live="form.price_one_liter" label="Цена за литр" type="number"/>
                            <flux:input wire:model.live="form.volume" label="Объем (л)" type="number"/>
                        </div>
                    </flux:card>
                </x-blocks.main-block>
            </flux:tab.panel>
            <flux:tab.panel name="stocks_warehouses">
                <x-marketPages.stocks-warehouses :market="$market"/>
                <livewire:wb-warehouse.wb-warehouse-index :market="$market" />
            </flux:tab.panel>
            <flux:tab.panel name="relationships_commissions">
                <x-marketPages.relationships-commissions :market="$market" :file="$file" market-name="wb"
                                                         sort-by="$sortBy" sort-direction="$sortDirection">
                    <flux:input wire:model="min_price" label="Минимальная цена продажи" type="number"/>
                    <flux:input wire:model="retail_markup_percent" label="Наценка" type="number"/>
                    <flux:input wire:model="package" label="Упаковка" type="number"/>
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
    @if($this->user()->can('update-wb'))
        {!! $this->renderSaveButton() !!}
    @endif
</div>
