@extends('adminlte::page')

@section('title', config('app.name'))
<!-- CDN SweetAlert2 -->
@section('content_header')
{{ $header ?? '' }}
@stop

@section('content')
{{ $slot }}
@stop