@extends('layouts.error')

@section('title', '403 - Forbidden')

@section('content')
@php
    $error = config('errors.403') ?? config('errors.default');
@endphp

@include('partials.error-hero', [
    'code'    => '403',
    'color'   => $error['color'],
    'icon'    => $error['icon'],
    'title'   => $error['title'],
    'message' => $error['message'],
])
@endsection