@extends('layouts.error')

@section('title', '400 - Bad Request')

@section('content')
@php
    $error = config('errors.400') ?? config('errors.default');
@endphp

@include('partials.error-hero', [
    'code'    => '400',
    'color'   => $error['color'],
    'icon'    => $error['icon'],
    'title'   => $error['title'],
    'message' => $error['message'],
])
@endsection