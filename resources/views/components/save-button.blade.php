@if($show)
    <x-blocks.main-block>
        <flux:button variant="primary" class="w-full !fixed !bottom-2" wire:click="update">Сохранить</flux:button>
    </x-blocks.main-block>
@endif
