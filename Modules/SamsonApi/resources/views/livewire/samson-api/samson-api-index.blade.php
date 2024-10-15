<x-layouts.module-index-layout :modules="$modules">
    <x-layouts.main-container>
        <x-navigate-pages>
            <x-links.tab-link href="{{route('samsonapi.index', ['page' => 'main'])}}" :active="$page === 'main'">
                Основное
            </x-links.tab-link>
            @if($form->samsonApi)
                <x-links.tab-link href="{{route('samsonapi.index', ['page' => 'times'])}}" :active="$page === 'times'">
                    Время выгрузки
                </x-links.tab-link>
                <x-links.tab-link href="{{route('samsonapi.index', ['page' => 'attributes'])}}"
                                  :active="$page === 'attributes'">Атрибуты
                </x-links.tab-link>
            @endif
        </x-navigate-pages>
    </x-layouts.main-container>
    @if($page === 'main')
        <x-layouts.main-container>
            <x-blocks.main-block>
                <flux:card class="space-y-6">
                    <div class="flex">
                        <div class="space-y-6">
                            <flux:button wire:click="store">Сохранить</flux:button>
                            <div>
                                <flux:select variant="combobox" placeholder="Выберите поставщика..." wire:model.live.debounce.1s="form.supplier_id" label="Поставщик">

                                    @foreach(auth()->user()->suppliers as $supplier)
                                        <flux:option :value="$supplier->getKey()">{{$supplier->name}}</flux:option>
                                    @endforeach
                                </flux:select>
                            </div>
                            @if($form->supplier_id)
                                <div>
                                    <flux:select variant="combobox" placeholder="Выберите склад поставщика..." wire:model="form.supplier_warehouse_id" label="Склад поставщика">

                                        @foreach(\App\Models\SupplierWarehouse::where('supplier_id', $form->supplier_id)->get() as $warehouse)
                                            <flux:option :value="$warehouse->getKey()">{{$warehouse->name}}</flux:option>
                                        @endforeach
                                    </flux:select>
                                </div>
                            @endif
                            <flux:input label="Апи ключ" wire:model="form.api_key" required/>
                        </div>
                    </div>
                </flux:card>
            </x-blocks.main-block>
        </x-layouts.main-container>
    @elseif($page === 'times')
        <livewire:samsonapi::samson-api-time.samson-api-time-index :samson-api="$form->samsonApi"/>
    @elseif($page === 'attributes')
        <livewire:samsonapi::samson-api-item-additional-attribute-link.samson-api-item-additional-attribute-link-index :samson-api="$form->samsonApi"/>
    @endif
    <div wire:loading wire:target="store">
        <x-loader/>
    </div>
</x-layouts.module-index-layout>
