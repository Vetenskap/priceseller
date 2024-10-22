@props(['market' => null, 'file' => null, 'directLink' => false, 'sortBy' => null, 'sortDirection' => null, 'marketName' => 'ozon'])

<div>
    @if($this->user()->can('update-' . $marketName) && $this->user()->can('create-' . $marketName) && $this->user()->can('delete-' . $marketName))
        @isset($slot)
            <x-blocks.main-block>
                <flux:card class="space-y-6">
                    <flux:heading size="xl">Комиссии</flux:heading>
                    <flux:subheading>Комиссии по умолчанию</flux:subheading>
                    <div class="flex gap-6 items-end">
                        {{$slot}}
                        <flux:button wire:click="updateUserCommissions">Обновить сейчас</flux:button>
                    </div>
                </flux:card>
            </x-blocks.main-block>
        @endisset
        <x-blocks.main-block>
            <flux:card class="space-y-6">
                <flux:heading size="xl">Создание/Обновление/Удаление связей и комиссий вручную</flux:heading>
                <x-blocks.center-block>
                    <flux:button wire:click="downloadTemplate">Скачать шаблон</flux:button>
                </x-blocks.center-block>
                <x-file-block action="import"/>
            </flux:card>
        </x-blocks.main-block>
        <x-blocks.main-block>
            <flux:card class="space-y-6">
                <flux:heading size="xl">Загрузка по апи</flux:heading>
                <div class="flex gap-6 items-center">
                    <flux:button wire:click="relationshipsAndCommissions">Загрузить/обновить связи и комиссии</flux:button>
                    <flux:switch wire:model="directLink" label="Прямая связь"/>
                </div>
                <flux:button wire:click="updateApiCommissions">Обновить комиссии</flux:button>
            </flux:card>
        </x-blocks.main-block>
        <x-blocks.main-block>
            <flux:card class="space-y-6">
                <flux:heading size="xl">История загрузок</flux:heading>
                <livewire:items-import-report.items-import-report-index :model="$market"/>
            </flux:card>
        </x-blocks.main-block>
    @endif
    <x-blocks.main-block>
        <flux:card class="space-y-6">
            <flux:heading size="xl">Все связи</flux:heading>
            @if($this->user()->can('delete-' . $marketName))
                <flux:button variant="danger" wire:click="clearRelationships">Очистить связи</flux:button>
            @endif
            <flux:card class="space-y-6">
                <flux:heading size="lg">Фильтры</flux:heading>
                <div class="flex gap-6 flex-wrap">
                    <flux:input wire:model.live.debounce.5s="filters.market_id" label="Идентификатор товара"/>
                    <flux:input wire:model.live.debounce.5s="filters.market_client_code" label="Код клиента"/>
                </div>
            </flux:card>
            @if($this->items->count() > 0)
                <flux:table :paginate="$this->items">
                    <flux:columns>
                        <flux:column>Идентификатор товара</flux:column>
                        <flux:column>Код клиента</flux:column>
                        <flux:column>Товар/Комплект</flux:column>
                        <flux:column sortable :sorted="$sortBy === 'price'" :direction="$sortDirection"
                                     wire:click="sort('price')">Цена</flux:column>
                        <flux:column sortable :sorted="$sortBy === 'created_at'" :direction="$sortDirection"
                                     wire:click="sort('created_at')">Дата создания</flux:column>
                        <flux:column sortable :sorted="$sortBy === 'updated_at'" :direction="$sortDirection"
                                     wire:click="sort('updated_at')">Дата обновления</flux:column>
                    </flux:columns>
                    <flux:rows>
                        @foreach($this->items as $item)
                            <flux:row :key="$item->getKey()">
                                <flux:cell>{{$item->product_id ?? $item->nm_id}}</flux:cell>
                                <flux:cell>{{$item->offer_id ?? $item->vendor_code}}</flux:cell>
                                <flux:cell>
                                    <flux:link :href="$item->ozonitemable ? ($item->ozonitemable instanceof App\Models\Item ? route('item-edit', ['item' => $item->ozonitemable->getKey()]) : route('bundles.edit', ['bundle' => $item->ozonitemable->getKey()])) : ($item->wbitemable instanceof App\Models\Item ? route('item-edit', ['item' => $item->wbitemable->getKey()]) : route('bundles.edit', ['bundle' => $item->wbitemable->getKey()]))">
                                        {{$item->ozonitemable ? $item->ozonitemable->code : $item->wbitemable->code}}
                                    </flux:link>
                                </flux:cell>
                                <flux:cell>{{$item->price}}</flux:cell>
                                <flux:cell>{{$item->created_at}}</flux:cell>
                                <flux:cell>{{$item->updated_at}}</flux:cell>
                                <flux:cell>
                                    <flux:button icon="pencil-square" :href="$item->ozonitemable ? route('ozon.item.edit', ['item' => $item->getKey()]) : route('wb.item.edit', ['item' => $item->getKey()])" />
                                </flux:cell>
                            </flux:row>
                        @endforeach
                    </flux:rows>
                </flux:table>
            @else
                <flux:subheading>Нет связей</flux:subheading>
            @endif
        </flux:card>
    </x-blocks.main-block>
</div>
