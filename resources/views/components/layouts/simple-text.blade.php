@props(['name'])
<div {{ $attributes->merge(['class' => "text-gray-900 dark:text-gray-100"]) }} title="{{$name}}">
    {{\Illuminate\Support\Str::limit($name, 35)}}
</div>
