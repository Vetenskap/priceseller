<div>
    <flux:dropdown>
        @if($notifications->isNotEmpty())
            <flux:button icon="bell-alert" variant="ghost" size="sm"/>
        @else
            <flux:button icon="bell" variant="ghost" icon-variant="outline" size="sm"/>
        @endif

        <flux:menu>
            @foreach($notifications as $notification)
                <flux:menu.item :href="$notification->href" :icon="$notification->status === 0 ? 'check-badge' : ($notification->status === 1 ? 'exclamation-triangle' : 'exclamation-circle')">{{$notification->message}}</flux:menu.item>
            @endforeach

            <flux:menu.separator />

            <flux:menu.item variant="danger" icon="trash" wire:click="clear">Очистить</flux:menu.item>
        </flux:menu>
    </flux:dropdown>
</div>