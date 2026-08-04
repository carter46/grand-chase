@php
    if ($settings->redirect_url != null || !empty($settings->redirect_url)) {
        header("Location: $settings->redirect_url", true, 301);
        exit();
    }
    $activeNav = 'investments';
@endphp
@extends('layouts.base')
@section('title', 'Investments')

@section('content')
@include('home.partials.investments-content')
@endsection
