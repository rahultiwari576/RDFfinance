<div class="modal fade" id="loanModal" tabindex="-1" aria-labelledby="loanModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="loanModalLabel">Apply for a New Loan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="loanApplicationForm"
                    action="{{ route('loans.apply') }}"
                    data-list-url="{{ route('loans.list') }}"
                    data-calculate-url="{{ route('emi.calculate') }}">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Loan Amount (Principal)</label>
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
                            <label class="form-label">Custom Penalty (₹)</label>
                            <input type="number" name="custom_penalty_amount" class="form-control" min="0" placeholder="Defaults to ₹{{ config('loan.default_penalty', 100) }}">
                        </div>
                    </div>
                    <div class="alert alert-info mt-3 d-none" id="loanCalculationPreview"></div>
                    <div class="mt-4 d-flex justify-content-end">
                        <button type="button" class="btn btn-outline-secondary me-2" id="previewLoan">Preview EMI</button>
                        <button type="submit" class="btn btn-primary">Submit Application</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

