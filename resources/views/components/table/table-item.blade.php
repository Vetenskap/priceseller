@props(['status' => -1])
@php
    switch ($status) {
        case 0:
            $classes = 'bg-green-300 hover:bg-green-400';
            break;
        case 1:
            $classes = 'bg-red-300 hover:bg-red-400';
            break;
        case 2:
            $classes = 'bg-yellow-100 hover:bg-yellow-200 dark:bg-yellow-600 dark:hover:bg-yellow-500';
            break;
        default:
            $classes = 'bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600';
            break;
    }
@endphp
<div {!! $attributes->merge(["class" => "flex cursor-pointer items-center justify-between p-4 " . $classes]) !!}>
    {{$slot}}
</div>
