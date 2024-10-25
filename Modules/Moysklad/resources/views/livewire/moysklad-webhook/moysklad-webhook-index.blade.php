<div>
    <x-layouts.main-container>
        <flux:navbar>
            @foreach($moysklad->webhooks as $wh)
                <flux:navbar.item :current="$webhook?->getKey() === $wh->getKey()" :href="route('moysklad.index', ['page' => 'webhooks', 'webhookId' => $wh->id])" :badge="$wh->reports()->count()" badge-color="lime">{{$wh->name}}</flux:navbar.item>
            @endforeach
        </flux:navbar>
    </x-layouts.main-container>
    @if($webhook)
        <x-layouts.main-container>
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
        </x-layouts.main-container>
    @endif
</div>
