<div>
    <x-blocks.main-block>
        <flux:navbar>
            @foreach($moysklad->webhooks as $wh)
                <flux:navbar.item :current="$webhook?->getKey() === $wh->getKey()"
                                  :href="route('moysklad.index', ['page' => 'webhooks', 'webhookId' => $wh->id])"
                                  :badge="$wh->reports()->count()" badge-color="lime">{{$wh->name}}</flux:navbar.item>
            @endforeach
        </flux:navbar>
    </x-blocks.main-block>
    @if($webhook)
        <x-blocks.main-block>
            <flux:card class="space-y-6">
                <div class="flex justify-between">
                    @if($webhook->enabled)
                        <flux:button wire:click="disable">Выключить</flux:button>
                    @else
                        <flux:button wire:click="enable">Включить</flux:button>
                    @endif
                    <flux:button variant="danger" wire:click="delete">Удалить</flux:button>
                </div>
                <livewire:moysklad::moysklad-webhook-report.moysklad-webhook-report-index
                    :webhook="$webhook"/>
            </flux:card>
        </x-blocks.main-block>
    @endif
</div>
