<?php

use Livewire\Volt\Component;

new class extends Component
{
    public \App\Models\Supplier $supplier;
    public Illuminate\Pagination\LengthAwarePaginator $priceItems;

    public function mount(): void
    {
        $this->priceItems = $this->supplier->priceItems()->paginate(10, pageName: 'price_page');
    }

}; ?>

<div>
    <x-blocks.main-block>
        <x-layouts.title name="Прайс"/>
    </x-blocks.main-block>
    @if($priceItems->isNotEmpty())
        <x-blocks.main-block>
            <x-titles.sub-title name="Фильтры"/>
        </x-blocks.main-block>
        <x-blocks.flex-block>
            <x-inputs.input-with-label name="article"
                                       type="text"
                                       field="filters.article"
            >Артикул
            </x-inputs.input-with-label>
        </x-blocks.flex-block>
        <x-table.table-layout>
            <x-table.table-header>
                <x-table.table-child>
                    <x-layouts.simple-text name="Статус"/>
                </x-table.table-child>
                <x-table.table-child>
                    <x-layouts.simple-text name="Артикул"/>
                </x-table.table-child>
                <x-table.table-child>
                    <x-layouts.simple-text name="Бренд"/>
                </x-table.table-child>
                <x-table.table-child>
                    <x-layouts.simple-text name="Цена"/>
                </x-table.table-child>
                <x-table.table-child>
                    <x-layouts.simple-text name="Остаток"/>
                </x-table.table-child>
            </x-table.table-header>
            @foreach($priceItems as $priceItem)
                <x-table.table-item wire:key="{{$priceItem->getKey()}}" :status="$priceItem->status">
                    <x-table.table-child>
                        <x-layouts.simple-text :name="$priceItem->message"/>
                    </x-table.table-child>
                    <x-table.table-child>
                        <x-layouts.simple-text :name="$priceItem->article"/>
                    </x-table.table-child>
                    <x-table.table-child>
                        <x-layouts.simple-text :name="$priceItem->brand"/>
                    </x-table.table-child>
                    <x-table.table-child>
                        <x-layouts.simple-text :name="$priceItem->price"/>
                    </x-table.table-child>
                    <x-table.table-child>
                        <x-layouts.simple-text :name="$priceItem->stock"/>
                    </x-table.table-child>
                </x-table.table-item>
            @endforeach
        </x-table.table-layout>
        <x-blocks.main-block>
            {{$priceItems->links()}}
        </x-blocks.main-block>
    @endif
</div>
