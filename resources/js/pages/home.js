import axios from 'axios';
import Swal from 'sweetalert2';

$(function () {
    const loansContainer = $('#loansContainer');
    const remindersContainer = $('#remindersContainer');
    const loanForm = $('#loanApplicationForm');

    const loansUrl = loanForm.data('listUrl');
    const calculateUrl = loanForm.data('calculateUrl');

    const fetchLoans = () => {
        loansContainer.html('<p class="text-muted">Loading loans...</p>');
        axios.get(loansUrl)
            .then(({ data }) => renderLoans(data.loans))
            .catch(() => loansContainer.html('<p class="text-danger">Failed to load loans.</p>'));
    };

    const fetchReminders = () => {
        remindersContainer.html('<p class="text-muted">Fetching reminders...</p>');
        axios.get(remindersContainer.data('url'))
            .then(({ data }) => renderReminders(data.reminders))
            .catch(() => remindersContainer.html('<p class="text-danger">Unable to fetch reminders.</p>'));
    };

    const renderLoans = (loans = []) => {
        if (!loans.length) {
            loansContainer.html('<p class="text-muted">No loans yet. Apply for your first loan.</p>');
            return;
        }

        const template = loans.map((loan) => {
            const installments = loan.installments.map((installment) => `
                <tr>
                    <td>${installment.due_date}</td>
                    <td>₹${Number(installment.amount).toFixed(2)}</td>
                    <td>${installment.status}</td>
                    <td>
                        ${installment.status !== 'paid'
                            ? `<button class="btn btn-sm btn-success mark-paid" data-url="${installment.pay_url}">Mark Paid</button>`
                            : `<span class="badge bg-success">Paid</span>`
                        }
                    </td>
                </tr>
            `).join('');

            return `
                <div class="card mb-3">
                    <div class="card-header d-flex justify-content-between">
                        <span>Loan #${loan.id} - ₹${Number(loan.principal_amount).toFixed(2)}</span>
                        <span>Status: <strong>${loan.status}</strong></span>
                    </div>
                    <div class="card-body">
                        <p class="mb-1"><strong>EMI:</strong> ₹${Number(loan.emi_amount).toFixed(2)}</p>
                        <p class="mb-1"><strong>Total Repayment:</strong> ₹${Number(loan.total_repayment).toFixed(2)}</p>
                        <p class="mb-1"><strong>Next Due:</strong> ${loan.next_due_date ?? 'N/A'}</p>
                        <div class="table-responsive mt-3">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Due Date</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>${installments}</tbody>
                            </table>
                        </div>
                    </div>
                </div>
            `;
        }).join('');

        loansContainer.html(template);
    };

    const renderReminders = (reminders = []) => {
        if (!reminders.length) {
            remindersContainer.html('<p class="text-muted">No pending EMI reminders.</p>');
            return;
        }

        const items = reminders.map((reminder) => `
            <li class="list-group-item d-flex justify-content-between">
                <span>Loan #${reminder.loan_id} - Due ${reminder.due_date}</span>
                <span>₹${Number(reminder.amount).toFixed(2)} + Penalty ₹${Number(reminder.penalty_amount).toFixed(2)}</span>
            </li>
        `).join('');

        remindersContainer.html(`<ul class="list-group">${items}</ul>`);
    };

    $('#previewLoan').on('click', function () {
        const previewAlert = $('#loanCalculationPreview');
        const formData = new FormData(loanForm[0]);

        axios.post(calculateUrl, formData, {
            headers: { 'Content-Type': 'multipart/form-data' },
        })
            .then(({ data }) => {
                const { results } = data;
                previewAlert.removeClass('d-none')
                    .html(`
                        <strong>Monthly EMI:</strong> ₹${Number(results.emi_amount).toFixed(2)}<br>
                        <strong>Total Repayment:</strong> ₹${Number(results.total_repayment).toFixed(2)}<br>
                        <strong>Tenure:</strong> ${results.tenure_months} months
                    `);
            })
            .catch(() => {
                previewAlert.addClass('d-none');
                Swal.fire('Error', 'Unable to preview EMI.', 'error');
            });
    });

    loanForm.on('submit', function (e) {
        e.preventDefault();
        const formData = new FormData(this);

        axios.post(loanForm.attr('action'), formData, {
            headers: { 'Content-Type': 'multipart/form-data' },
        })
            .then(({ data }) => {
                Swal.fire('Success', data.message, 'success');
                $('#loanModal').modal('hide');
                fetchLoans();
                fetchReminders();
            })
            .catch((error) => {
                const message = error.response?.data?.message || 'Loan application failed.';
                Swal.fire('Error', message, 'error');
            });
    });

    loansContainer.on('click', '.mark-paid', function () {
        const url = $(this).data('url');

        Swal.fire({
            title: 'Mark EMI as Paid',
            html: '<input type="number" id="penaltyInput" class="swal2-input" placeholder="Custom Penalty (optional)">',
            showCancelButton: true,
            confirmButtonText: 'Confirm',
            preConfirm: () => document.getElementById('penaltyInput').value,
        }).then((result) => {
            if (result.isConfirmed) {
                axios.post(url, { custom_penalty_amount: result.value || null })
                    .then(() => {
                        Swal.fire('Updated', 'Installment marked as paid.', 'success');
                        fetchLoans();
                        fetchReminders();
                    })
                    .catch(() => Swal.fire('Error', 'Could not update installment.', 'error'));
            }
        });
    });

    $('#refreshLoans').on('click', fetchLoans);
    $('#refreshReminders').on('click', fetchReminders);

    fetchLoans();
    fetchReminders();
});

