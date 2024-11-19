<x-layouts.module-index-layout :modules="$modules">
    <x-blocks.main-block>
        <flux:navbar>
            <flux:navbar.item :href="route('samsonapi.index', ['page' => 'main'])" :current="$page === 'main'">
                Основное
            </flux:navbar.item>
            @if($form->samsonApi)
                <flux:navbar.item :href="route('samsonapi.index', ['page' => 'times'])" :current="$page === 'times'">Время
                    выгрузки
                </flux:navbar.item>
                <flux:navbar.item :href="route('samsonapi.index', ['page' => 'attributes'])"
                                  :current="$page === 'attributes'">Атрибуты
                </flux:navbar.item>
            @endif
        </flux:navbar>
    </x-blocks.main-block>
    @if($page === 'main')
        <x-blocks.main-block>
            <flux:card class="space-y-6">
                <flux:select variant="combobox" placeholder="Выберите поставщика..."
                             wire:model.live.debounce.1s="form.supplier_id" label="Поставщик">

                    @foreach(auth()->user()->suppliers as $supplier)
                        <flux:option :value="$supplier->getKey()">{{$supplier->name}}</flux:option>
                    @endforeach
                </flux:select>
                @if($form->supplier_id)
                    <flux:select variant="combobox" placeholder="Выберите склад поставщика..."
                                 wire:model.live="form.supplier_warehouse_id" label="Склад поставщика">

                        @foreach(\App\Models\SupplierWarehouse::where('supplier_id', $form->supplier_id)->get() as $warehouse)
                            <flux:option :value="$warehouse->getKey()">{{$warehouse->name}}</flux:option>
                        @endforeach
                    </flux:select>
                @endif
                <flux:input label="Апи ключ" wire:model.live="form.api_key" required />
            </flux:card>
        </x-blocks.main-block>
        {!! $this->renderSaveButton() !!}
    @elseif($page === 'times')
        <livewire:samsonapi::samson-api-time.samson-api-time-index :samson-api="$form->samsonApi"/>
    @elseif($page === 'attributes')
        <livewire:samsonapi::samson-api-item-additional-attribute-link.samson-api-item-additional-attribute-link-index
            :samson-api="$form->samsonApi"/>
    @endif
    <div wire:loading wire:target="store">
        <x-loader/>
    </div>
</x-layouts.module-index-layout>
