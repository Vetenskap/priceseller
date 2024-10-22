<x-layouts.module-index-layout :modules="$modules">
    <x-layouts.main-container>
        <flux:tab.group>
            <x-blocks.main-block>
                <flux:tabs>
                    <flux:tab name="general" icon="home">Главная</flux:tab>
                    <flux:tab name="settings" icon="wrench-screwdriver">Настройки</flux:tab>
                </flux:tabs>
            </x-blocks.main-block>

            <flux:tab.panel name="general">
                <div class="rounded-lg shadow-md">
                    @if(auth()->user()->ozonMarkets()->exists())
                        <x-blocks.main-block>
                            <flux:heading size="xl">ОЗОН</flux:heading>
                        </x-blocks.main-block>
                        @foreach(auth()->user()->ozonMarkets as $market)
                            <x-blocks.main-block>
                                <flux:card>
                                    <div class="flex gap-6 items-center">
                                        <flux:heading size="lg">{{$market->name}}</flux:heading>
                                        @foreach($market->warehouses as $warehouse)
                                            <flux:button>{{$warehouse->name}}</flux:button>
                                        @endforeach
                                    </div>
                                </flux:card>
                            </x-blocks.main-block>
                        @endforeach
                    @endif
                    @if(auth()->user()->wbMarkets()->exists())
                        <x-blocks.main-block>
                            <flux:heading size="xl">ВБ</flux:heading>
                        </x-blocks.main-block>
                        @foreach(auth()->user()->wbMarkets as $market)
                            <x-blocks.main-block>
                                <flux:card>
                                    <div class="flex gap-6 items-center">
                                        <flux:heading size="lg">{{$market->name}}</flux:heading>
                                        @foreach($market->warehouses as $warehouse)
                                            <flux:button>{{$warehouse->name}}</flux:button>
                                        @endforeach
                                    </div>
                                </flux:card>
                            </x-blocks.main-block>
                        @endforeach
                    @endif
                </div>
            </flux:tab.panel>
            <flux:tab.panel name="settings">
                <x-blocks.main-block>
                    <flux:card class="space-y-6">
                        <flux:heading size="xl">Карточка ОЗОН</flux:heading>
                    </flux:card>
                </x-blocks.main-block>
                <x-blocks.main-block>
                    <flux:card class="space-y-6">
                        <flux:heading size="xl">Карточка ВБ</flux:heading>
                        <div>
                            <flux:card>
                                <div class="flex gap-12">
                                    <flux:heading
                                        :size="match($headingLevel) { '1' => 'base', '2' => 'lg', '3' => 'xl' }">
                                        Наименование
                                    </flux:heading>
                                    <flux:input type="range" min="1" max="3" step="1" wire:model.live="headingLevel"/>
                                </div>
                            </flux:card>
                            <flux:card>
                                <div class="flex">
                                    <flux:card class="w-1/4 space-y-6 text-center">
                                        <img src="https://www.susu.ru/sites/default/files/field/image/1_53.png" />
                                        <flux:button variant="danger" size="sm">Пожаловаться</flux:button>
                                    </flux:card>
                                    <flux:card class="space-y-6 w-full">
                                        <div class="flex gap-12">
                                            <flux:button
                                                :size="match($headingButton) { '1' => 'xs', '2' => 'sm', '3' => 'base' }">
                                                Получить этикетку
                                            </flux:button>
                                            <flux:input type="range" min="1" max="3" step="1"
                                                        wire:model.live="headingButton"/>
                                        </div>
                                        @foreach($selectedFields as $ssfield => $parameters)
                                            <div class="flex gap-12" wire:key="{{$ssfield}}">

                                                <flux:button icon="chevron-{{$this->upOrDown($ssfield)}}" wire:click="upField({{json_encode($ssfield)}})"/>

                                                <div class="flex">

                                                </div>
                                                @if($parameters['level'] < 5)
                                                    <flux:subheading
                                                        class="text-nowrap"
                                                        style="color: {{ $parameters['color'] }};"
                                                        :size="match($parameters['level']) { '1' => 'sm', '2' => 'default', '3' => 'lg', '4' => 'xl' }">{{$ssfield}}</flux:subheading>
                                                @else
                                                    <flux:heading
                                                        class="text-nowrap"
                                                        style="color: {{ $parameters['color'] }};"
                                                        :size="match($parameters['level']) { '5' => 'base', '6' => 'lg', '7' => 'xl' }">{{$ssfield}}</flux:heading>
                                                @endif

                                                <flux:input type="color"
                                                            wire:model.live="selectedFields.{{$ssfield}}.color"/>

                                                <flux:input type="range" min="1" max="7" step="1"
                                                            wire:model.live="selectedFields.{{$ssfield}}.level"/>
                                            </div>
                                        @endforeach
                                        <div>
                                            <flux:dropdown>
                                                <flux:button icon-trailing="chevron-down">Добавить поле</flux:button>
                                                <flux:menu>
                                                    @foreach($fields as $name => $subfields)
                                                        <flux:menu.submenu heading="{{$name}}">
                                                            @foreach($subfields as $field)
                                                                <flux:menu.item wire:click="addField({{json_encode($field)}})">{{$field}}</flux:menu.item>
                                                            @endforeach
                                                        </flux:menu.submenu>
                                                    @endforeach
                                                </flux:menu>
                                            </flux:dropdown>
                                        </div>
                                    </flux:card>
                                </div>
                                <flux:card>
                                    <div class="flex gap-12">
                                        @foreach($additionalFields as $ssfield => $parameters)
                                            <div wire:key="{{$ssfield}}">

                                                @if($parameters['level'] < 5)
                                                    <flux:subheading
                                                        class="text-nowrap"
                                                        style="color: {{ $parameters['color'] }};"
                                                        :size="match($parameters['level']) { '1' => 'sm', '2' => 'default', '3' => 'lg', '4' => 'xl' }">{{$ssfield}}</flux:subheading>
                                                @else
                                                    <flux:heading
                                                        class="text-nowrap"
                                                        style="color: {{ $parameters['color'] }};"
                                                        :size="match($parameters['level']) { '5' => 'base', '6' => 'lg', '7' => 'xl' }">{{$ssfield}}</flux:heading>
                                                @endif

                                                <flux:input type="color"
                                                            wire:model.live="additionalFields.{{$ssfield}}.color"/>

                                                <flux:input type="range" min="1" max="7" step="1"
                                                            wire:model.live="additionalFields.{{$ssfield}}.level"/>
                                            </div>
                                        @endforeach
                                            <flux:dropdown>
                                                <flux:button icon-trailing="chevron-down">Добавить поле</flux:button>
                                                <flux:menu>
                                                    @foreach($fields as $name => $subfields)
                                                        <flux:menu.submenu heading="{{$name}}">
                                                            @foreach($subfields as $field)
                                                                <flux:menu.item wire:click="addAdditionalField({{json_encode($field)}})">{{$field}}</flux:menu.item>
                                                            @endforeach
                                                        </flux:menu.submenu>
                                                    @endforeach
                                                </flux:menu>
                                            </flux:dropdown>
                                    </div>
                                </flux:card>
                            </flux:card>
                        </div>
                    </flux:card>
                </x-blocks.main-block>
            </flux:tab.panel>
        </flux:tab.group>
    </x-layouts.main-container>
</x-layouts.module-index-layout>
