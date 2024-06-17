@php
dd('тест');
@endphp
<div>
    <x-layouts.header name="Требуется подписка"/>
    <x-layouts.main-container>
        <x-blocks.main-block>
            <x-layouts.title name="Для управления приложением требуется подписка"/>
        </x-blocks.main-block>
        <x-blocks.center-block>
            <x-success-button>Подписаться</x-success-button>
        </x-blocks.center-block>
    </x-layouts.main-container>
</div>

