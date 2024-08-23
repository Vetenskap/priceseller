@props(['modules'])

<div>
    <x-layouts.header name="Модули"/>
    <x-layouts.module-container class="flex p-6">
        <div class="p-6 dark:bg-gray-700 bg-gray-100 w-1/4 shadow-sm sm:rounded-lg mr-6">
            @foreach($modules as $module)
                <a href="{{route(config(\Illuminate\Support\Str::lower($module->name) . ".main_route"))}}">
                    <div
                        class="flex justify-between mb-6 text-center shadow-sm sm:rounded-lg p-4 dark:text-white {{str_contains(request()->getUri(), \Illuminate\Support\Str::lower($module->name)) ? 'dark:bg-gray-600 bg-gray-300' : 'dark:bg-gray-500 bg-gray-200'}}">
                        <div>
                            {{config(\Illuminate\Support\Str::lower($module->name) . ".name")}}
                        </div>
                        <div>
                            <x-inputs.switcher :checked="auth()->user()->modules()->where('module_id', $module->id)->first()?->enabled" wire:click="changeOpen({{$module}})" disabled="{{!\App\Services\ModuleService::moduleIsVisible($module->name, auth()->user())}}"/>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
        <div class="p-6 dark:bg-gray-700 bg-gray-100 w-3/4 shadow-sm sm:rounded-lg mr-6">
            {{$slot}}
        </div>
    </x-layouts.module-container>
</div>
