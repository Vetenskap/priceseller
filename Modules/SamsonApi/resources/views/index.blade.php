@extends('samsonapi::layouts.master')

@section('content')
    <h1>Hello World</h1>

    <p>Module: {!! config('samsonapi.name') !!}</p>
@endsection
