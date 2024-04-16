@props(['name', 'active' => false])
<a {!! $attributes->merge(["class" => "text-gray-600 py-4 px-6 block hover:text-blue-500 focus:outline-none cursor-pointer " . ($active ? 'text-blue-500 border-b-2 font-medium border-blue-500' : '')]) !!}>
    {{$name ?: $slot}}
</a>
