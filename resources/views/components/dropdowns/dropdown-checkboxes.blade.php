@props(['options', 'selectedOptions', 'optionName' => 'name', 'optionValue' => 'id', 'wireFunc'])

<div x-data="{active: false}" class="w-[250px]">
    <button class="text-nowrap overflow-hidden dropdown-button w-[250px] inline-flex h-[42px] items-center px-4 justify-between font-medium text-gray-700 rounded-md shadow-sm focus:outline-none border border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600"
            @click="active = ! active">
        {{$slot}}
        <div>
            <div x-show="active" id="close">
                <svg width="10" height="6" viewBox="0 0 10 6" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M5.00016 0.666664L9.66683 5.33333L0.333496 5.33333L5.00016 0.666664Z" fill="#1F2937"/>
                </svg>
            </div>
            <div id="open" x-show="!active">
                <svg width="10" height="6" viewBox="0 0 10 6" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M5.00016 5.33333L0.333496 0.666664H9.66683L5.00016 5.33333Z" fill="#1F2937"/>
                </svg>
            </div>
        </div>
    </button>
    <div class="max-h-52 absolute mt-2 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 p-1 space-y-1 dark:bg-gray-900 w-[250px]" id="dropdown" x-show="active">
        @foreach($options as $option)
            <div class="flex items-center justify-between mb-2">
                <div class="flex items-center">
                    <div class="pl-4 flex items-center">
                        <div class="bg-gray-100 dark:bg-gray-800 border rounded-sm border-gray-200 dark:border-gray-700 w-3 h-3 flex flex-shrink-0 justify-center items-center relative">
                            <input aria-labelledby="fb1" type="checkbox" class="focus:opacity-100 checkbox opacity-0 absolute cursor-pointer w-full h-full" wire:change="{{ $wireFunc }}('{{ $option[$optionValue] }}')"
                                   @if(in_array($option[$optionValue], $selectedOptions)) checked @endif/>
                            <div class="check-icon hidden bg-indigo-700 text-white rounded-sm">
                                <svg class="icon icon-tabler icon-tabler-check" xmlns="http://www.w3.org/2000/svg"
                                     width="12" height="12" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                     fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z"/>
                                    <path d="M5 12l5 5l10 -10"/>
                                </svg>
                            </div>
                        </div>
                        <p id="fb1" tabindex="0"
                           class="focus:outline-none text-md leading-normal ml-2 text-gray-800">{{$option[$optionName]}}</p>
                    </div>
                </div>
                {{--            <p tabindex="0" class="focus:outline-none w-8 text-xs leading-3 text-right text-indigo-700">2,381</p>--}}
            </div>
        @endforeach
    </div>
</div>
<style>.checkbox:checked + .check-icon {
        display: flex;
    }
</style>
