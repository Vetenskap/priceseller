<div>
    <flux:dropdown>
        <flux:button
            :icon="$notifications->isNotEmpty() ? 'bell-alert' : 'bell'"
            variant="ghost"
            :icon-variant="$notifications->isNotEmpty() ? null : 'outline'"
            size="sm"
        />

        <flux:menu class="h-1/3 overflow-y-scroll">
            @foreach($notifications as $notification)
                <flux:menu.item
                    :href="$notification->href"
                    :icon="match($notification->status) {
                    0 => 'check-badge',
                    1 => 'exclamation-triangle',
                    default => 'exclamation-circle',
                }"
                >
                    {{$notification->message}}
                </flux:menu.item>
            @endforeach

            @if($hasMore)
                <flux:menu.item wire:click="loadMore">Загрузить больше...</flux:menu.item>
            @endif

            <flux:menu.separator />

            <flux:menu.item
                variant="danger"
                icon="trash"
                wire:click="clear"
            >
                Очистить
            </flux:menu.item>
        </flux:menu>
    </flux:dropdown>
</div>
