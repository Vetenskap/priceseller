<div>
    <x-layouts.header name="Модули"/>
    <x-layouts.module-container class="flex p-6">
        <div class="p-6 dark:bg-gray-700 bg-gray-100 w-1/4 overflow-hidden shadow-sm sm:rounded-lg mr-6">
            @foreach(Module::allEnabled() as $name => $module)
                <a href="{{route(config(\Illuminate\Support\Str::lower($name) . ".main_route"))}}">
                    <div
                        class="mb-6 text-center shadow-sm sm:rounded-lg p-4 dark:text-white {{request()->routeIs(\Illuminate\Support\Str::lower($name) . ".main_route") ? 'dark:bg-gray-600 bg-gray-300' : 'dark:bg-gray-500 bg-gray-200'}}">
                        {{config(\Illuminate\Support\Str::lower($name) . ".name")}}
                    </div>
                </a>
            @endforeach
        </div>
    </x-layouts.module-container>
</div>
