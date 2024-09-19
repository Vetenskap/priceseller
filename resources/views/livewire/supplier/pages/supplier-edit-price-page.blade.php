<?php

use Livewire\Volt\Component;

new class extends Component
{
    public \App\Models\Supplier $supplier;

    public function download(): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        return Excel::download(new \App\Exports\EmailPriceItemsExport($this->supplier), 'отчёт_прайс.xlsx');
    }
}; ?>

<div>
    <x-blocks.main-block>
        <x-layouts.title name="Прайс"/>
    </x-blocks.main-block>
    <x-blocks.flex-block>
        <x-success-button wire:click="download">Скачать</x-success-button>
    </x-blocks.flex-block>
    <div wire:loading wire:target="download">
        <x-loader />
    </div>
</div>
