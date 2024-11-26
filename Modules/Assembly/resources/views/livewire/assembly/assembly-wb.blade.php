<div x-data="{ hrefImg: '' }">
    <div class="py-2">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            <div class='bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg'>
                <x-blocks.main-block>
                    <div class="space-y-6">
                        <flux:modal.trigger name="create-supply">
                            <flux:button icon="plus">Создать поставку</flux:button>
                        </flux:modal.trigger>
                        <flux:card class="space-y-6">
                            <flux:accordion>
                                <flux:accordion.item>
                                    <flux:accordion.heading>Сортировка</flux:accordion.heading>

                                    <flux:accordion.content>
                                        <div class="lg:flex gap-6 mt-6">
                                            @foreach(array_merge($fields, $additionalFields) as $field => $parameters)
                                                @if($field === $sortBy)
                                                    @if($sortDirection === 'desc')
                                                        <div>
                                                            <flux:button class="!w-full"
                                                                         wire:target="sort({{json_encode($field)}})"
                                                                         wire:click="sort({{json_encode($field)}})"
                                                                         icon-trailing="chevron-down">{{$parameters['label']}}</flux:button>
                                                        </div>
                                                    @else
                                                        <div>
                                                            <flux:button class="!w-full"
                                                                         wire:target="sort({{json_encode($field)}})"
                                                                         wire:click="sort({{json_encode($field)}})"
                                                                         icon-trailing="chevron-up">{{$parameters['label']}}</flux:button>
                                                        </div>
                                                    @endif
                                                @else
                                                    <div>
                                                        <flux:button class="!w-full"
                                                                     wire:target="sort({{json_encode($field)}})"
                                                                     wire:click="sort({{json_encode($field)}})"
                                                                     icon-trailing="chevron-down">{{$parameters['label']}}</flux:button>
                                                    </div>
                                                @endif
                                            @endforeach
                                        </div>
                                    </flux:accordion.content>
                                </flux:accordion.item>
                            </flux:accordion>
                        </flux:card>
                    </div>
                </x-blocks.main-block>
                <x-blocks.main-block>
                    @if(count($orders) > 0)
                        @foreach($orders as $order)
                            <div wire:key="{{$order->getId()}}">
                                <flux:card>
                                    <flux:heading
                                            :size="match($mainFields['name_heading']['size_level']) { '1' => 'base', '2' => 'lg', '3' => 'xl' }">
                                        {{$order->getCard()->getTitle()}}
                                    </flux:heading>
                                </flux:card>
                                <flux:card class="space-y-6">
                                    <div class="w-full flex gap-2">
                                        <div class="w-1/4 text-center">
                                            <flux:modal.trigger name="view-img">
                                                <img
                                                        x-on:click="hrefImg = '{{ $order->getCard()->getPhotos()->first()?->get('big') ?? '' }}'"
                                                        src="{{$order->getCard()->getPhotos()->first()?->get('big') ?? null}}"/>
                                            </flux:modal.trigger>
                                            <div class="sm:hidden">
                                                <flux:button variant="danger" icon="no-symbol" size="sm"/>
                                            </div>
                                            <div class="hidden sm:block">
                                                <flux:button variant="danger" size="sm">Пожаловаться</flux:button>
                                            </div>
                                        </div>
                                        <flux:separator vertical/>
                                        <div class="space-y-2">
                                            <flux:checkbox label="В поставку"
                                                           wire:model.live="selectedOrders.{{$order->getId()}}"/>
                                            <flux:separator/>
                                            @foreach($fields as $field => $parameters)
                                                @php
                                                    $value = null;
                                                    switch ($parameters['type']) {
                                                        case 'item':
                                                            if ($field === 'code') $value = $order->getCard()->getProduct()?->itemable[$field];
                                                            if ($order->getCard()->getProduct()?->itemable instanceof \App\Models\Item) {
                                                                $value = $order->getCard()->getProduct()?->itemable[$field];
                                                            }
                                                            break;
                                                        case 'attribute_item':
                                                            if ($order->getCard()->getProduct()?->itemable instanceof \App\Models\Item) {
                                                                $value = $order->getCard()->getProduct()?->itemable->attributesValues()->where('item_attribute_id', $field)->first()->value;
                                                            }
                                                            break;
                                                        case 'product':
                                                            $value = $product->getCard()->getProduct()[$field];
                                                            break;
                                                        case 'order':
                                                                $value = $order->{\Illuminate\Support\Str::camel('get' . $field)}($this->currentUser());
                                                            break;
                                                        case 'order_product':
                                                                $value = $order->getCard()->{\Illuminate\Support\Str::camel('get' . $field)}();
                                                            break;
                                                        case 'item_stocks':
                                                            if ($order->getCard()->getProduct()?->itemable instanceof \App\Models\Item) {
                                                                $value = $order->getCard()->getProduct()?->itemable->warehousesStocks()->sum('stock');
                                                            }
                                                            break;
                                                    }
                                                    if ($value instanceof \Illuminate\Support\Collection) $value = $value->toJson(JSON_UNESCAPED_UNICODE);
                                                    if (is_bool($value)) $value = $value ? 'да' : 'нет';
                                                @endphp
                                                @if(
                                                    ($order->getCard()->getProduct()?->itemable instanceof \App\Models\Bundle && $field === 'code') ||
                                                    ($order->getCard()->getProduct()?->itemable instanceof \App\Models\Bundle && $parameters['type'] !== 'item' && $parameters['type'] !== 'item_stocks') ||
                                                    $order->getCard()->getProduct()?->itemable instanceof \App\Models\Item
                                                )
                                                    <div class="lg:flex items-end gap-2" wire:key="{{$field}}">
                                                        <flux:subheading>{{$parameters['label']}}:</flux:subheading>
                                                        @if($parameters['size_level'] < 5)
                                                            <flux:subheading
                                                                    style="color: {{ $parameters['color'] }};"
                                                                    :size="match($parameters['size_level']) { '1' => 'sm', '2' => 'default', '3' => 'lg', '4' => 'xl' }">{{$value}}</flux:subheading>
                                                        @else
                                                            <flux:heading
                                                                    style="color: {{ $parameters['color'] }};"
                                                                    :size="match($parameters['size_level']) { '5' => 'base', '6' => 'lg', '7' => 'xl' }">{{$value}}</flux:heading>
                                                        @endif
                                                    </div>
                                                    <flux:separator/>
                                                @endif
                                            @endforeach
                                            <flux:card class="flex gap-4">
                                                @foreach($additionalFields as $field => $parameters)
                                                    @php
                                                        $value = null;
                                                        switch ($parameters['type']) {
                                                            case 'item':
                                                                if ($order->getCard()->getProduct()?->itemable instanceof \App\Models\Item) {
                                                                    $value = $order->getCard()->getProduct()?->itemable[$field];
                                                                }
                                                                break;
                                                            case 'attribute_item':
                                                                if ($order->getCard()->getProduct()?->itemable instanceof \App\Models\Item) {
                                                                    $value = $order->getCard()->getProduct()?->itemable->attributesValues()->where('item_attribute_id', $field)->first()->value;
                                                                }
                                                                break;
                                                            case 'product':
                                                                $value = $product->getCard()->getProduct()[$field];
                                                                break;
                                                            case 'order':
                                                                    $value = $order->{\Illuminate\Support\Str::camel('get' . $field)}();
                                                                break;
                                                            case 'order_product':
                                                                    $value = $order->getCard()->{\Illuminate\Support\Str::camel('get' . $field)}();
                                                                break;
                                                        }
                                                        $value = (bool) $value
                                                    @endphp
                                                    <div wire:key="{{$field}}">
                                                        @if($value)
                                                            @if($parameters['size_level'] < 5)
                                                                <flux:subheading
                                                                        class="text-nowrap"
                                                                        style="color: {{ $parameters['color'] }};"
                                                                        :size="match($parameters['size_level']) { '1' => 'sm', '2' => 'default', '3' => 'lg', '4' => 'xl' }">{{$parameters['label']}}</flux:subheading>
                                                            @else
                                                                <flux:heading
                                                                        class="text-nowrap"
                                                                        style="color: {{ $parameters['color'] }};"
                                                                        :size="match($parameters['size_level']) { '5' => 'base', '6' => 'lg', '7' => 'xl' }">{{$parameters['label']}}</flux:heading>
                                                            @endif
                                                        @endif
                                                    </div>
                                                @endforeach
                                            </flux:card>
                                        </div>
                                    </div>
                                    @if(!empty(Arr::where($fields, fn($item) => $item['in_table'] ?? false)) && $order->getCard()->getProduct()?->itemable instanceof \App\Models\Bundle)
                                        <flux:card class="space-y-6">
                                            <flux:heading size="xl">Состав комплекта</flux:heading>
                                            <flux:table>
                                                <flux:columns>
                                                    <flux:column>#</flux:column>
                                                    @foreach($fields as $field => $parameters)
                                                        @if(isset($parameters['in_table']) && $parameters['in_table'])
                                                            <flux:column>{{$parameters['label']}}</flux:column>
                                                        @endif
                                                    @endforeach
                                                </flux:columns>
                                                <flux:rows>
                                                    @foreach($order->getCard()->getProduct()?->itemable->items as $item)
                                                        <flux:row>
                                                            <flux:cell>1</flux:cell>
                                                            @foreach($fields as $field => $parameters)
                                                                @if(isset($parameters['in_table']) && $parameters['in_table'])
                                                                    @if($parameters['type'] === 'item_stocks')
                                                                        <flux:cell>{{$item->warehousesStocks()->sum('stock')}}</flux:cell>
                                                                    @else
                                                                        <flux:cell>{{$item[$field]}}</flux:cell>
                                                                    @endif
                                                                @endif
                                                            @endforeach
                                                        </flux:row>
                                                    @endforeach
                                                </flux:rows>
                                            </flux:table>
                                        </flux:card>
                                    @endif
                                </flux:card>
                            </div>
                        @endforeach
                    @endif
                </x-blocks.main-block>
            </div>
        </div>
    </div>
    <flux:modal name="view-img" class="w-full">
        <img x-bind:src="hrefImg"/>
    </flux:modal>
    <flux:modal name="create-supply" class="md:w-96 space-y-6">
        <div>
            <flux:heading size="lg">Создание поставки</flux:heading>
            <flux:subheading>Количество товаров: {{count($selectedOrders)}}.</flux:subheading>
            <flux:error name="selectedOrders"/>
        </div>
        <flux:input label="Наименование поставки" wire:model="supplyName" required/>
        <div class="flex">
            <flux:spacer/>

            <flux:button variant="primary" wire:click="createSupply">Создать поставку</flux:button>
        </div>
    </flux:modal>
</div>
