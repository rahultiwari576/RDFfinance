@extends('layouts.app')

@section('title', 'Welcome')

@section('content')
<div class="text-center py-5">
    <h1 class="display-5">Welcome to {{ config('app.name') }}</h1>
    <p class="lead">Secure authentication, smart loan management, and powerful EMI tools in one place.</p>
    <div class="mt-4">
        <a href="{{ route('login') }}" class="btn btn-primary btn-lg me-2">Login</a>
        <a href="{{ route('register') }}" class="btn btn-outline-primary btn-lg me-2">Register</a>
        <a href="{{ route('emi.guest') }}" class="btn btn-success btn-lg">Try Smart EMI Calculator</a>
    </div>
</div>
@endsection

