<div x-data="{ hrefImg: '' }">
    <div class="py-2">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            <div class='bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg'>
                <x-blocks.main-block>
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
                </x-blocks.main-block>
                <x-blocks.main-block>
                    @if(count($postings) > 0)
                        <div class="space-y-6 mt-6">
                            @foreach($postings as $posting)
                                <flux:card class="!border-cyan-300 border-4">
                                    @foreach($posting->getProducts() as $product)
                                        @php
                                            $product->fetchAttribute($warehouse->market);
                                            $product->loadLink($warehouse->market);
                                        @endphp
                                        <div>
                                            <flux:card>
                                                <flux:heading
                                                    :size="match($mainFields['name_heading']['size_level']) { '1' => 'base', '2' => 'lg', '3' => 'xl' }">
                                                    {{$product->getName()}}
                                                </flux:heading>
                                            </flux:card>
                                            <flux:card class="space-y-6">
                                                <div class="w-full flex gap-2">
                                                    <div class="w-1/4 text-center">
                                                        <flux:modal.trigger name="view-img">
                                                            <img
                                                                x-on:click="hrefImg = '{{$product->getAttribute()->getImages()->first()->get('file_name') ?? ''}}'"
                                                                src="{{$product->getAttribute()->getImages()->first()->get('file_name') ?? null}}"/>
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
                                                        <div class="flex gap-12">
                                                            <flux:button
                                                                wire:click="createLabel({{json_encode($posting->getPostingNumber())}})"
                                                                wire:target="createLabel({{json_encode($posting->getPostingNumber())}})"
                                                                :size="match($mainFields['button_label']['size_level']) { '1' => 'xs', '2' => 'sm', '3' => 'base' }">
                                                                Получить этикетку
                                                            </flux:button>
                                                        </div>
                                                        @foreach($fields as $field => $parameters)
                                                            @php
                                                                $value = null;
                                                                switch ($parameters['type']) {
                                                                    case 'item':
                                                                        if ($field === 'code') $value = $product->getProduct()?->itemable[$field];
                                                                        if ($product->getProduct()?->itemable instanceof \App\Models\Item) {
                                                                            $value = $product->getProduct()?->itemable[$field];
                                                                        }
                                                                        break;
                                                                    case 'attribute_item':
                                                                        $value = $product->getProduct()?->itemable->attributesValues()->where('item_attribute_id', $field)->first()->value;
                                                                        break;
                                                                    case 'product':
                                                                        $value = $product->getProduct()[$field];
                                                                        break;
                                                                    case 'order':
                                                                        $value = $posting->{\Illuminate\Support\Str::camel('get' . $field)}();
                                                                        break;
                                                                    case 'order_product':
                                                                        $value = $product->{\Illuminate\Support\Str::camel('get' . $field)}();
                                                                        break;
                                                                    case 'order_attribute_product':
                                                                        $value = $product->getAttribute()->{\Illuminate\Support\Str::camel('get' . $field)}();
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
                                                                ($product->getProduct()?->itemable instanceof \App\Models\Bundle && $field === 'code') ||
                                                            ($product->getProduct()?->itemable instanceof \App\Models\Bundle && $parameters['type'] !== 'item' && $parameters['type'] !== 'item_stocks') ||
                                                            $product->getProduct()?->itemable instanceof \App\Models\Item
                                                            )

                                                            @endif
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
                                                                                if ($field === 'code') $value = $product->getProduct()?->itemable[$field];
                                                                                if ($product->getProduct()?->itemable instanceof \App\Models\Item) {
                                                                                    $value = $product->getProduct()?->itemable[$field];
                                                                                }
                                                                                break;
                                                                            case 'attribute_item':
                                                                                $value = $product->getProduct()?->itemable->attributesValues()->where('item_attribute_id', $field)->first()->value;
                                                                                break;
                                                                            case 'product':
                                                                                $value = $product->getProduct()[$field];
                                                                                break;
                                                                            case 'order':
                                                                                $value = $posting->{\Illuminate\Support\Str::camel('get' . $field)}();
                                                                                break;
                                                                            case 'order_product':
                                                                                $value = $product->{\Illuminate\Support\Str::camel('get' . $field)}();
                                                                                break;
                                                                            case 'order_attribute_product':
                                                                                $value = $product->getAttribute()->{\Illuminate\Support\Str::camel('get' . $field)}();
                                                                                break;
                                                                        }
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
                                                                @foreach($product->getProduct()?->itemable->items as $item)
                                                                    <flux:row>
                                                                        <flux:cell>1</flux:cell>
                                                                        @foreach($fields as $field => $parameters)
                                                                            @if($parameters['type'] === 'item_stocks')
                                                                                <flux:cell>{{$item->warehousesStocks()->sum('stock')}}</flux:cell>
                                                                            @else
                                                                                <flux:cell>{{$item[$field]}}</flux:cell>
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
                                </flux:card>
                            @endforeach
                        </div>
                    @else
                        <flux:subheading>Нет заказов</flux:subheading>
                    @endif
                </x-blocks.main-block>
            </div>
        </div>
    </div>
    <flux:modal name="view-img" class="w-full">
        <img x-bind:src="hrefImg"/>
    </flux:modal>
    @script
    <script>
        $wire.on('openPdf', (event) => {
            const pdfBase64 = event[0]['pdfBase64'];

            // Декодируем base64 и создаем blob
            const byteCharacters = atob(pdfBase64);
            const byteNumbers = new Array(byteCharacters.length);
            for (let i = 0; i < byteCharacters.length; i++) {
                byteNumbers[i] = byteCharacters.charCodeAt(i);
            }
            const byteArray = new Uint8Array(byteNumbers);
            const blob = new Blob([byteArray], {type: 'application/pdf'});

            // Создаем URL и открываем в новой вкладке
            const url = URL.createObjectURL(blob);
            window.open(url, '_blank');
        });
    </script>
    @endscript
</div>
