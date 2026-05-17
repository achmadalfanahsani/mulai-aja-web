@extends('layouts.error')

@section('title', '401 - Unauthorized')

@section('content')
@php
    $error = config('errors.401') ?? config('errors.default');
@endphp

@include('partials.error-hero', [
    'code'    => '401',
    'color'   => $error['color'],
    'icon'    => $error['icon'],
    'title'   => $error['title'],
    'message' => $error['message'],
])
@endsection