<div>
    <x-layouts.main-container>
        <x-blocks.main-block>
            <x-layouts.title name="Добавление нового времени выгрузки"/>
        </x-blocks.main-block>
        <x-blocks.flex-block-end>
            <x-inputs.time-picker name="time" field="time">Время</x-inputs.time-picker>
            @if(!($samsonApi->times()->count() >= 3))
                <div class="self-center">
                    <x-success-button wire:click="store">Добавить</x-success-button>
                </div>
            @endif
        </x-blocks.flex-block-end>
    </x-layouts.main-container>
    @if($samsonApi->times->isNotEmpty())
        <x-layouts.main-container>
            <x-blocks.main-block>
                <x-layouts.title name="Список" />
            </x-blocks.main-block>
            @foreach($samsonApi->times as $time)
                <livewire:samsonapi::samson-api-time.samson-api-time-edit :samson-api-time="$time" wire:key="{{$time->getKey()}}"/>
            @endforeach
        </x-layouts.main-container>
    @endif
</div>
