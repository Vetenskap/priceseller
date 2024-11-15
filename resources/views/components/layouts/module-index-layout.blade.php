@props(['modules'])

<div>
    <x-layouts.header name="Модули"/>
    <div class="md:flex p-6 sm:px-6 lg:px-8">
        <div class="py-6 xl:w-1/4 lg:w-1/3 md:w-1/2">
            <div class="sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6 space-y-6">
                    @foreach($modules as $module)
                        <flux:card>
                            <div class="flex justify-between">
                                <a href="{{route(config(\Illuminate\Support\Str::lower($module->name) . ".main_route"))}}"
                                   class="{{str_contains(request()->getUri(), \Illuminate\Support\Str::lower($module->name)) ? 'text-gray-500' : ''}}">
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
            </div>
        </div>
        <div class="py-6 w-full">
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg">
                {{$slot}}
            </div>
        </div>
    </div>
</div>
