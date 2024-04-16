<div>
    <x-layouts.header name="Авито"/>
    <x-layouts.main-container>
        <div class="bg-white">
            <nav class="flex flex-col sm:flex-row">
                <x-links.tab-link name="Основное" :href="route('avito')" :active="request()->routeIs('avito')" wire:navigate.hover/>
                <x-links.tab-link name="Склады" href=""/>
            </nav>
        </div>
    </x-layouts.main-container>
</div>
