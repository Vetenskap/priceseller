<div>
    <x-layouts.main-container>
        <x-blocks.center-block>
            <x-layouts.title name="Вебхуки"/>
        </x-blocks.center-block>
        <x-blocks.flex-block>
            @foreach($moysklad->webhooks as $wh)
                <a href="{{route('moysklad.index', ['page' => 'webhooks', 'webhookId' => $wh->id])}}">
                    <div
                        class="w-[250px] mb-6 text-center shadow-sm sm:rounded-lg p-4 dark:text-white {{$webhookId === $wh->id ? 'dark:bg-gray-600 bg-gray-300' : 'dark:bg-gray-500 bg-gray-200'}}">
                        {{$wh->name}}
                    </div>
                </a>
            @endforeach
        </x-blocks.flex-block>
    </x-layouts.main-container>
    @if($webhook)
        <x-layouts.main-container>
            <x-layouts.actions>
                @if($webhook->enabled)
                    <x-primary-button wire:click="disable">Выключить</x-primary-button>
                @else
                    <x-secondary-button wire:click="enable">Включить</x-secondary-button>
                @endif
                <x-danger-button wire:click="delete">Удалить</x-danger-button>
            </x-layouts.actions>
            <x-blocks.main-block>
                <x-layouts.title :name="$webhook->name"/>
            </x-blocks.main-block>
            <livewire:moysklad::moysklad-webhook-report.moysklad-webhook-report-index
                :webhook-reports="$webhook->reports"/>
        </x-layouts.main-container>
    @endif
    <div wire:loading wire:target="delete, enable, disable">
        <x-loader/>
    </div>
</div>
