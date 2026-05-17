@extends('layouts.error')

@section('title', '500 - Internal Server Error')

@section('content')
@php
    $error = config('errors.500') ?? config('errors.default');
@endphp

@include('partials.error-hero', [
    'code'    => '500',
    'color'   => $error['color'],
    'icon'    => $error['icon'],
    'title'   => $error['title'],
    'message' => $error['message'],
])
@endsection