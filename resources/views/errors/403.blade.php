@extends('errors::minimal')

@section('title', 'Forbidden')
@section('code', '403')
@section('message', isset($exception) && $exception->getMessage() ? $exception->getMessage() : 'Forbidden')
