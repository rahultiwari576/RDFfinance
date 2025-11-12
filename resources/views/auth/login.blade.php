@extends('layouts.app')

@section('title', 'Login')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Login</h5>
                <a href="{{ route('register') }}">Register</a>
            </div>
            <div class="card-body">
                <ul class="nav nav-tabs mb-3" id="loginTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="email-tab" data-bs-toggle="tab" data-bs-target="#email-login" type="button" role="tab">Email & Password</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="aadhar-tab" data-bs-toggle="tab" data-bs-target="#aadhar-login" type="button" role="tab">Aadhar & OTP</button>
                    </li>
                </ul>
                <div class="tab-content" id="loginTabsContent">
                    <div class="tab-pane fade show active" id="email-login" role="tabpanel">
                        <form id="emailLoginForm" action="{{ route('login.email') }}">
                            @csrf
                            <div class="mb-3">
                                <label for="email" class="form-label">Email address</label>
                                <input type="email" name="email" id="email" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" name="password" id="password" class="form-control" required>
                            </div>
                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="remember" name="remember">
                                <label class="form-check-label" for="remember">Remember me</label>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Login</button>
                        </form>
                    </div>
                    <div class="tab-pane fade" id="aadhar-login" role="tabpanel">
                        <form id="aadharLoginForm" action="{{ route('login.aadhar') }}">
                            @csrf
                            <div class="mb-3">
                                <label for="aadhar_number" class="form-label">Aadhar Number</label>
                                <input type="text" name="aadhar_number" id="aadhar_number" class="form-control" maxlength="12" required>
                            </div>
                            <button type="submit" class="btn btn-dark w-100">Send OTP</button>
                        </form>
                        <form id="otpVerificationForm" action="{{ route('login.otp') }}" class="mt-3 d-none">
                            @csrf
                            <input type="hidden" name="otp_id" id="otp_id">
                            <div class="mb-3">
                                <label for="otp_code" class="form-label">Enter OTP</label>
                                <input type="text" name="code" id="otp_code" class="form-control" maxlength="6" required>
                            </div>
                            <button type="submit" class="btn btn-success w-100">Verify OTP</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ mix('js/auth.js') }}"></script>
@endpush

