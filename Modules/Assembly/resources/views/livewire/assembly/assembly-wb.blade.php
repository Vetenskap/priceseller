<div x-data="{ hrefImg: '' }">
    <div class="py-2">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            <div class='bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg'>
                <x-blocks.main-block>
                    <flux:dropdown>
                        <flux:button icon-trailing="chevron-down">Сортировка</flux:button>
                        <flux:menu>
                            @foreach(array_merge($fields, $additionalFields) as $field => $parameters)
                                <flux:menu.item>
                                    @if($sortDirection === 'desc')
                                        @if($sortBy === $field)
                                            <flux:button variany="primary" wire:click="sort({{json_encode($field)}})"
                                                         icon-trailing="chevron-down">{{$parameters['label']}}</flux:button>
                                        @else
                                            <flux:button wire:click="sort({{json_encode($field)}})"
                                                         icon-trailing="chevron-down">{{$parameters['label']}}</flux:button>
                                        @endif
                                    @else
                                        @if($sortBy === $field)
                                            <flux:button variant="primary" wire:click="sort({{json_encode($field)}})"
                                                         icon-trailing="chevron-up">{{$parameters['label']}}</flux:button>
                                        @else
                                            <flux:button wire:click="sort({{json_encode($field)}})"
                                                         icon-trailing="chevron-up">{{$parameters['label']}}</flux:button>
                                        @endif
                                    @endif
                                </flux:menu.item>
                            @endforeach
                        </flux:menu>
                    </flux:dropdown>
                </x-blocks.main-block>
                <x-blocks.main-block>
                    <flux:modal.trigger name="create-supply">
                        <flux:button icon="plus">Создать поставку</flux:button>
                    </flux:modal.trigger>
                </x-blocks.main-block>
                <x-blocks.main-block>
                    @if(count($orders) > 0)
                        <div class="space-y-6 mt-6">
                            @foreach($orders as $order)
                                <flux:card>
                                    <flux:card>
                                        <flux:heading
                                                :size="match($mainFields['name_heading']['size_level']) { '1' => 'base', '2' => 'lg', '3' => 'xl' }">
                                            {{$order->getCard()->getTitle()}}
                                        </flux:heading>
                                    </flux:card>
                                    <div class="flex">
                                        <flux:card class="w-1/4 space-y-6 text-center">
                                            <flux:modal.trigger name="view-img">
                                                <img
                                                        x-on:click="hrefImg = '{{ $order->getCard()->getPhotos()->first()?->get('big') ?? '' }}'"
                                                        src="{{$order->getCard()->getPhotos()->first()?->get('big') ?? null}}"/>
                                            </flux:modal.trigger>
                                            <flux:button variant="danger" size="sm">Пожаловаться</flux:button>
                                        </flux:card>
                                        <flux:card class="space-y-4 w-full">
                                            <flux:checkbox label="В поставку"
                                                           wire:model.live="selectedOrders.{{$order->getId()}}"/>
                                            <flux:separator/>
                                            @foreach($fields as $field => $parameters)
                                                @php
                                                    $value = null;
                                                    switch ($parameters['type']) {
                                                        case 'item':
                                                            if ($order->getCard()->getProduct()->itemable instanceof \App\Models\Item) {
                                                                $value = $order->getCard()->getProduct()->itemable[$field];
                                                            }
                                                            break;
                                                        case 'attribute_item':
                                                            if ($order->getCard()->getProduct()->itemable instanceof \App\Models\Item) {
                                                                $value = $order->getCard()->getProduct()->itemable->attributesValues()->where('item_attribute_id', $field)->first()->value;
                                                            }
                                                            break;
                                                        case 'product':
                                                            $value = $product->getCard()->getProduct()[$field];
                                                            break;
                                                        case 'order':
                                                                $value = $order->{'get' . \Illuminate\Support\Str::apa($field)}();
                                                            break;
                                                        case 'order_product':
                                                                $value = $order->getCard()->{'get' . \Illuminate\Support\Str::apa($field)}();
                                                            break;
                                                    }
                                                    if ($value instanceof \Illuminate\Support\Collection) $value = $value->toJson(JSON_UNESCAPED_UNICODE);
                                                    if (is_bool($value)) $value = $value ? 'да' : 'нет';
                                                @endphp
                                                <div class="flex items-end gap-2" wire:key="{{$field}}">
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
                                            @endforeach
                                            <flux:card>
                                                <div class="flex gap-4">
                                                    @foreach($additionalFields as $field => $parameters)
                                                        @php
                                                            $value = null;
                                                            switch ($parameters['type']) {
                                                                case 'item':
                                                                    if ($order->getCard()->getProduct()->itemable instanceof \App\Models\Item) {
                                                                        $value = $order->getCard()->getProduct()->itemable[$field];
                                                                    }
                                                                    break;
                                                                case 'attribute_item':
                                                                    if ($order->getCard()->getProduct()->itemable instanceof \App\Models\Item) {
                                                                        $value = $order->getCard()->getProduct()->itemable->attributesValues()->where('item_attribute_id', $field)->first()->value;
                                                                    }
                                                                    break;
                                                                case 'product':
                                                                    $value = $product->getCard()->getProduct()[$field];
                                                                    break;
                                                                case 'order':
                                                                        $value = $order->{'get' . \Illuminate\Support\Str::apa($field)}();
                                                                    break;
                                                                case 'order_product':
                                                                        $value = $order->getCard()->{'get' . \Illuminate\Support\Str::apa($field)}();
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
                                                </div>
                                            </flux:card>
                                        </flux:card>
                                    </div>
                                </flux:card>
                            @endforeach
                        </div>
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
            <flux:error name="selectedOrders" />
        </div>
        <flux:input label="Наименование поставки" wire:model="supplyName" required/>
        <div class="flex">
            <flux:spacer/>

            <flux:button variant="primary" wire:click="createSupply">Создать поставку</flux:button>
        </div>
    </flux:modal>
</div>
