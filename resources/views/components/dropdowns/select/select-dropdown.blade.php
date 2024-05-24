@props(['value' => null])

<div>
    <label for="select" class="block font-medium text-sm text-gray-700 dark:text-gray-300">Select Input:</label>

    <div class="relative">
        <div class="h-10 bg-white flex border border-gray-200 rounded items-center">
            <input value="{{$value}}" name="select" id="select"
                   class="px-4 appearance-none outline-none text-gray-800 w-full" checked/>

            <button
                class="cursor-pointer outline-none focus:outline-none transition-all text-gray-300 hover:text-gray-600">
                <svg class="w-4 h-4 mx-2 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                     stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>
            <label for="show_more"
                   class="cursor-pointer outline-none focus:outline-none border-l border-gray-200 transition-all text-gray-300 hover:text-gray-600">
                <svg class="w-4 h-4 mx-2 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                     stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="18 15 12 9 6 15"></polyline>
                </svg>
            </label>
        </div>

        <input type="checkbox" name="show_more" id="show_more" class="hidden peer" checked/>
        <div class="absolute rounded shadow bg-white overflow-hidden hidden peer-checked:flex flex-col w-full mt-1 border border-gray-200">
            {{$slot}}
        </div>
    </div>
</div>
