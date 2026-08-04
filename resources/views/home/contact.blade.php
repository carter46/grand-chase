@php
    if ($settings->redirect_url != null || !empty($settings->redirect_url)) {
        header("Location: $settings->redirect_url", true, 301);
        exit();
    }
    $activeNav = 'contact';
@endphp
@extends('layouts.base')
@section('title', 'Contact')

@section('content')
@include('home.partials.contact-content')
@endsection
