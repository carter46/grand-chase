@php
    if ($settings->redirect_url != null || !empty($settings->redirect_url)) {
        header("Location: $settings->redirect_url", true, 301);
        exit();
    }
    $activeNav = 'business';
@endphp
@extends('layouts.base')
@section('title', 'Business Banking')

@section('content')
@include('home.partials.business-content')
@endsection
