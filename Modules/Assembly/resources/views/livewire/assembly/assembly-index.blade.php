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
                    <flux:card>
                        <flux:heading size="xl">Карточка ВБ</flux:heading>
                        <flux:card>
                            <flux:heading size="xl">Наименование</flux:heading>
                        </flux:card>
                        <flux:card class="space-y-6">
                            <div class="flex">
                                <flux:card>
                                    Фото
                                </flux:card>
                            </div>
                        </flux:card>
                    </flux:card>
                </x-blocks.main-block>
            </flux:tab.panel>
        </flux:tab.group>
    </x-layouts.main-container>
</x-layouts.module-index-layout>
