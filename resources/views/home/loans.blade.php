@php
    if ($settings->redirect_url != null || !empty($settings->redirect_url)) {
        header("Location: $settings->redirect_url", true, 301);
        exit();
    }
    $activeNav = 'loans';
@endphp
@extends('layouts.base')
@section('title', 'Loans')

@section('content')
@include('home.partials.loans-content')
@endsection
