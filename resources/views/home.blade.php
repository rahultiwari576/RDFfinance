@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="row g-4">
    <div class="col-lg-4">
        <div class="card shadow-sm">
            <div class="card-header">Profile</div>
            <div class="card-body">
                <p class="mb-1"><strong>Name:</strong> {{ $user->name }}</p>
                <p class="mb-1"><strong>Email:</strong> {{ $user->email }}</p>
                <p class="mb-1"><strong>Aadhar:</strong> {{ $user->aadhar_number }}</p>
                <p class="mb-1"><strong>PAN:</strong> {{ $user->pan_number }}</p>
                <p class="mb-0"><strong>Phone:</strong> {{ $user->phone_number }}</p>
            </div>
        </div>
        <div class="card shadow-sm mt-3">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>Quick Actions</span>
                <a class="btn btn-link btn-sm" href="{{ route('emi.guest') }}">Smart EMI Calculator</a>
            </div>
            <div class="card-body">
                <button class="btn btn-success w-100" data-bs-toggle="modal" data-bs-target="#loanModal">Apply New Loan</button>
            </div>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="card shadow-sm mb-3">
            <div class="card-header">Loan Summary</div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col">
                        <h5>₹{{ number_format($loanSummary['total_principal'], 2) }}</h5>
                        <p class="text-muted">Total Principal</p>
                    </div>
                    <div class="col">
                        <h5>₹{{ number_format($loanSummary['total_repayment'], 2) }}</h5>
                        <p class="text-muted">Total Repayment</p>
                    </div>
                    <div class="col">
                        <h5>₹{{ number_format($loanSummary['pending_amount'], 2) }}</h5>
                        <p class="text-muted">Pending Amount</p>
                    </div>
                    <div class="col">
                        <h5>{{ $loanSummary['pending_installments'] }}</h5>
                        <p class="text-muted">Pending EMIs</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm mb-3">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>Your Loans</span>
                <button class="btn btn-outline-primary btn-sm" id="refreshLoans">Refresh</button>
            </div>
            <div class="card-body" id="loansContainer">
                <p class="text-muted">Loading loans...</p>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>EMI Reminders</span>
                <button class="btn btn-outline-secondary btn-sm" id="refreshReminders">Refresh</button>
            </div>
            <div class="card-body" id="remindersContainer" data-url="{{ route('loans.reminders') }}">
                <p class="text-muted">Fetching reminders...</p>
            </div>
        </div>
    </div>
</div>

@include('partials.loan-modal')
@endsection

@push('scripts')
<script src="{{ mix('js/home.js') }}"></script>
@endpush

