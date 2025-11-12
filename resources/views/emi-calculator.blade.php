@extends('layouts.app')

@section('title', 'Smart EMI Calculator')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header">
                <h5 class="mb-0">Smart EMI Calculator</h5>
            </div>
            <div class="card-body">
                <form id="guestEmiForm" action="{{ route('emi.calculate') }}">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Loan Amount (₹)</label>
                            <input type="number" name="principal_amount" class="form-control" required min="1000">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Interest Rate (%)</label>
                            <input type="number" name="interest_rate" class="form-control" required step="0.1" min="1">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Tenure (Months)</label>
                            <input type="number" name="tenure_months" class="form-control" required min="1">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Penalty (₹)</label>
                            <input type="number" name="custom_penalty_amount" class="form-control" min="0" placeholder="Default ₹{{ config('loan.default_penalty', 100) }}">
                        </div>
                    </div>
                    <div class="d-flex justify-content-end mt-3">
                        <button type="submit" class="btn btn-primary">Calculate</button>
                    </div>
                </form>
                <div class="mt-4 d-none" id="guestEmiResults">
                    <h6>Results</h6>
                    <p class="mb-1"><strong>Monthly EMI:</strong> <span id="guestEmiAmount"></span></p>
                    <p class="mb-1"><strong>Total Repayment:</strong> <span id="guestTotalRepayment"></span></p>
                    <p class="mb-3"><strong>Penalty Applied:</strong> <span id="guestPenalty"></span></p>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Due Date</th>
                                    <th>Amount (₹)</th>
                                </tr>
                            </thead>
                            <tbody id="guestEmiSchedule"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ mix('js/emi-calculator.js') }}"></script>
@endpush

