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
                    @if(count($postings) > 0)
                        <div class="space-y-6 mt-6">
                            @foreach($postings as $posting)
                                <flux:card class="!border-cyan-300 border-4">
                                    @foreach($posting['products'] as $product)
                                        <flux:card>
                                            <flux:heading
                                                :size="match($mainFields['name_heading']['size_level']) { '1' => 'base', '2' => 'lg', '3' => 'xl' }">
                                                {{$product['name']}}
                                            </flux:heading>
                                        </flux:card>
                                        <div class="flex">
                                            <flux:card class="w-1/4 space-y-6 text-center">
                                                <flux:modal.trigger name="view-img">
                                                    <img
                                                        x-on:click="hrefImg = '{{$product['attribute']['images'][0]['file_name'] ?? ''}}'"
                                                        src="{{$product['attribute']['images'][0]['file_name'] ?? null}}"/>
                                                </flux:modal.trigger>
                                                <flux:button variant="danger" size="sm">Пожаловаться</flux:button>
                                            </flux:card>
                                            <flux:card class="space-y-4 w-full">
                                                <div class="flex gap-12">
                                                    <flux:button
                                                        wire:click="createLabel({{json_encode($posting['posting_number'])}})"
                                                        wire:target="createLabel({{json_encode($posting['posting_number'])}})"
                                                        :size="match($mainFields['button_label']['size_level']) { '1' => 'xs', '2' => 'sm', '3' => 'base' }">
                                                        Получить этикетку
                                                    </flux:button>
                                                </div>
                                                @foreach($fields as $field => $parameters)
                                                    @php
                                                        $value = null;
                                                        switch ($parameters['type']) {
                                                            case 'item':
                                                                $value = $product['product']->itemable[$field];
                                                                break;
                                                            case 'attribute_item':
                                                                $value = $product['product']->itemable->attributesValues()->where('item_attribute_id', $field)->first()->value;
                                                                break;
                                                            case 'product':
                                                                $value = $product['product'][$field];
                                                                break;
                                                            case 'order':
                                                                $value = $posting[$field];
                                                                break;
                                                            case 'order_product':
                                                                $value = $product[$field];
                                                                break;
                                                            case 'order_attribute_product':
                                                                $value = $product['attribute'][$field];
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
                                                                        $value = (bool) $product['product']->itemable[$field];
                                                                        break;
                                                                    case 'attribute_item':
                                                                        $value = (bool) $product['product']->itemable->attributesValues()->where('item_attribute_id', $field)->first()->value;
                                                                        break;
                                                                    case 'product':
                                                                        $value = (bool) $product['product'][$field];
                                                                        break;
                                                                    case 'order':
                                                                        $value = (bool) $posting[$field];
                                                                        break;
                                                                    case 'order_product':
                                                                        $value = (bool) $product[$field];
                                                                        break;
                                                                    case 'order_attribute_product':
                                                                        $value = (bool) $product['attribute'][$field];
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
                                            </flux:card>
                                        </div>
                                    @endforeach
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
