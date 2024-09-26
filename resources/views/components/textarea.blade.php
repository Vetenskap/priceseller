@props(['info' => "", 'name', 'value' => ""])

<div class="max-w-2xl mx-auto">

    <label for="message" class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray-400">{{$name}}</label>
    <textarea id="message" rows="4" {{ $attributes->merge(['class' => "block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"]) }} placeholder="Введите текст..." wire:model="{{$value}}"></textarea>

    <p class="mt-5">{{$info}}</p>
</div>
