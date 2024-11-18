@if($show)
    <flux:button class="w-full !fixed !bottom-2 !bg-[#64cdff]" wire:click="update">Сохранить</flux:button>
@else
    <flux:button variant="filled" class="w-full !fixed !bottom-2" wire:click="update">Сохранить</flux:button>
@endif
