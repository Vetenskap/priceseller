@extends('assembly::layouts.master')

@section('content')
    <h1>Hello World</h1>

    <p>Module: {!! config('assembly.name') !!}</p>
@endsection
