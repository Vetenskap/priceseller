@props(['modules'])

<div>
    <x-layouts.header name="Модули"/>
    <x-layouts.module-container class="flex p-6">
        <div class="p-6 w-1/4 shadow-sm sm:rounded-lg mr-6 space-y-6">
            @foreach($modules as $module)
                <flux:card>
                    <div class="flex justify-between">
                        <a href="{{route(config(\Illuminate\Support\Str::lower($module->name) . ".main_route"))}}" class="{{str_contains(request()->getUri(), \Illuminate\Support\Str::lower($module->name)) ? 'text-gray-500' : ''}}">
                            {{config(\Illuminate\Support\Str::lower($module->name) . ".name")}}
                        </a>
                        <div>
                            <flux:separator vertical/>
                            <flux:switch
                                wire:model.live="changeOpen.{{$module->id}}"
                                />
                        </div>
                    </div>
                </flux:card>
            @endforeach
        </div>
        <div class="p-6 dark:bg-gray-700 bg-gray-100 w-3/4 shadow-sm sm:rounded-lg mr-6">
            {{$slot}}
        </div>
    </x-layouts.module-container>
</div>
