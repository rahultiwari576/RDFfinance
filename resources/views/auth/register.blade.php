@extends('layouts.app')

@section('title', 'Register')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Create Account</h5>
                <a href="{{ route('login') }}">Login</a>
            </div>
            <div class="card-body">
                <form id="registrationForm" action="{{ route('register.submit') }}" data-extract-url="{{ route('register.extract-aadhar') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Name</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Confirm Password</label>
                            <input type="password" name="password_confirmation" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Age</label>
                            <input type="number" name="age" class="form-control" min="18" max="120" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Phone Number</label>
                            <input type="text" name="phone_number" class="form-control" maxlength="10" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Aadhar Number</label>
                            <input type="text" name="aadhar_number" id="aadhar_number_field" class="form-control" maxlength="12" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Upload Aadhar (PDF/Image)</label>
                            <input type="file" name="aadhar_document" id="aadhar_document" class="form-control" accept=".pdf,.jpg,.jpeg,.png" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Upload PAN (PDF/Image)</label>
                            <input type="file" name="pan_document" class="form-control" accept=".pdf,.jpg,.jpeg,.png" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">PAN Number</label>
                            <input type="text" name="pan_number" class="form-control" maxlength="10" required>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mt-4">
                        <button type="button" class="btn btn-secondary" id="extractAadharButton">Extract Aadhar From File</button>
                        <button type="submit" class="btn btn-primary">Register</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ mix('js/registration.js') }}"></script>
@endpush

