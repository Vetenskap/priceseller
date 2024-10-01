<x-layouts.module-index-layout :modules="$modules">
    <x-layouts.main-container>
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
    </x-layouts.main-container>
</x-layouts.module-index-layout>
