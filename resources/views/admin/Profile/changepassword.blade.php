<?php
if (Auth('admin')->User()->dashboard_style == 'light') {
    $text = 'dark';
} else {
    $text = 'light';
}
?>
@extends('layouts.app')
@section('content')
    @include('admin.topmenu')
    @include('admin.sidebar')
    <div class="main-panel ">
        <div class="content ">
            <div class="page-inner">
                <div class="mt-2 mb-5">
                    <h1 class="title1 ">Change Your password</h1> <br> <br>
                </div>
                <x-danger-alert />
                <x-success-alert />
                <div class="mb-5 row">
                    <div class="col-lg-8 offset-lg-2 card p-4  shadow">
                        <form method="post" action="{{ route('adminupdatepass') }}">
                            @csrf
                            <div class="">
                                <h5 class=" ">Old Password</h5>
                                <input type="password" name="old_password" class="form-control  " required autocomplete="current-password">
                            </div>
                            <div class="">
                                <h5 class=" ">New Password* </h5>
                                <input type="password" name="password" class="form-control  " required autocomplete="new-password">
                            </div>
                            <div class="">
                                <h5 class=" ">Confirm Password</h5>
                                <input type="password" name="password_confirmation" class="form-control  " required autocomplete="new-password">
                            </div> <br>
                            <input type="submit" class="btn btn-primary" value="Submit">
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endsection
