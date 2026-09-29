@extends('adminlte::page')

@section('title', config('app.name'))

@section('content_header')
    {{ $header ?? '' }}
@stop

@section('content')
    {{-- {{ $slot }} --}}
@stop