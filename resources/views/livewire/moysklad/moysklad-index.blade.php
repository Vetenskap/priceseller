<div>
    <x-layouts.header name="Мой склад"/>
    <x-layouts.main-container>
        <div class="bg-white">
            <nav class="flex flex-col sm:flex-row">
                <x-links.tab-link name="Основное" :href="route('moysklad')" :active="request()->routeIs('moysklad')" wire:navigate.hover/>
                <x-links.tab-link name="Склады" href=""/>
            </nav>
        </div>
        @if(request()->routeIs('moysklad'))
            <x-blocks.flex-block>
                <x-inputs.input-with-label name="name"
                                           type="text"
                                           field="name"
                >Наименование</x-inputs.input-with-label>
                <x-inputs.input-with-label name="api_key"
                                           type="text"
                                           field="api_key"
                >АПИ ключ</x-inputs.input-with-label>
            </x-blocks.flex-block>
        @endif
    </x-layouts.main-container>
</div>
