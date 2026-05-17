@extends('layouts.error')

@section('title', '404 - Not Found')

@section('content')
@php
    $error = config('errors.404') ?? config('errors.default');
@endphp

@include('partials.error-hero', [
    'code'    => '404',
    'color'   => $error['color'],
    'icon'    => $error['icon'],
    'title'   => $error['title'],
    'message' => $error['message'],
])
@endsection