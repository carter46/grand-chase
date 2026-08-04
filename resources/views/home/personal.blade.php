@php
    if ($settings->redirect_url != null || !empty($settings->redirect_url)) {
        header("Location: $settings->redirect_url", true, 301);
        exit();
    }
    $activeNav = 'personal';
@endphp
@extends('layouts.base')
@section('title', 'Personal Banking')

@section('content')
@include('home.partials.personal-content')
@endsection
