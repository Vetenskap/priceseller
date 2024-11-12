<x-layouts.module-index-layout :modules="$modules">
    <flux:tab.group>
        <x-blocks.main-block>
            <flux:tabs>
                <flux:tab name="general" icon="home">Главная</flux:tab>
                <flux:tab name="settings" icon="wrench-screwdriver">Настройки</flux:tab>
            </flux:tabs>
        </x-blocks.main-block>

        <flux:tab.panel name="general">
            @if(auth()->user()->ozonMarkets()->exists())
                <x-blocks.main-block>
                    <flux:card class="space-y-6">
                        <flux:heading size="xl">ОЗОН</flux:heading>
                        @foreach(auth()->user()->ozonMarkets as $market)
                            <flux:card>
                                <div class="flex gap-6 items-center">
                                    <flux:heading size="lg">{{$market->name}}</flux:heading>
                                    @foreach($market->warehouses as $warehouse)
                                        <flux:button
                                                :href="route('assembly.ozon', ['warehouse' => $warehouse])">{{$warehouse->name}}</flux:button>
                                    @endforeach
                                </div>
                            </flux:card>
                        @endforeach
                        <flux:button>Шрихкоды</flux:button>
                    </flux:card>
                </x-blocks.main-block>
            @endif
            @if(auth()->user()->wbMarkets()->exists())
                <x-blocks.main-block>
                    <flux:card class="space-y-6">
                        <flux:heading size="xl">ВБ</flux:heading>
                        <flux:card class="space-y-6">
                            <flux:heading size="xl">Сборочные задания</flux:heading>
                            @foreach(auth()->user()->wbMarkets as $market)
                                <flux:card>
                                    <div class="flex gap-6 items-center">
                                        <flux:heading size="lg">{{$market->name}}</flux:heading>
                                        @foreach($market->warehouses as $warehouse)
                                            <flux:button
                                                :href="route('assembly.wb', ['warehouse' => $warehouse])">{{$warehouse->name}}</flux:button>
                                        @endforeach
                                    </div>
                                </flux:card>
                            @endforeach
                        </flux:card>
                        <flux:card class="space-y-6">
                            <flux:heading size="xl">Поставки</flux:heading>
                        </flux:card>
                    </flux:card>
                </x-blocks.main-block>
            @endif
        </flux:tab.panel>
        <flux:tab.panel name="settings">
            <livewire:assembly::assembly-settings.assembly-settings-index/>
        </flux:tab.panel>
    </flux:tab.group>
</x-layouts.module-index-layout>
