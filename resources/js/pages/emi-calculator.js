import axios from 'axios';
import Swal from 'sweetalert2';

$(function () {
    const form = $('#guestEmiForm');
    const resultsContainer = $('#guestEmiResults');
    const emiAmount = $('#guestEmiAmount');
    const totalRepayment = $('#guestTotalRepayment');
    const penalty = $('#guestPenalty');
    const scheduleBody = $('#guestEmiSchedule');

    form.on('submit', function (e) {
        e.preventDefault();

        const formData = new FormData(this);

        axios.post(form.attr('action') ?? form.data('url'), formData, {
            headers: { 'Content-Type': 'multipart/form-data' },
        })
            .then(({ data }) => {
                const { results } = data;
                emiAmount.text(`₹${Number(results.emi_amount).toFixed(2)}`);
                totalRepayment.text(`₹${Number(results.total_repayment).toFixed(2)}`);
                penalty.text(`₹${Number(formData.get('custom_penalty_amount') || window.DEFAULT_PENALTY || 100).toFixed(2)}`);

                scheduleBody.html(results.schedule.map((item) => `
                    <tr>
                        <td>${item.installment_number}</td>
                        <td>${item.due_date}</td>
                        <td>₹${Number(item.amount).toFixed(2)}</td>
                    </tr>
                `).join(''));

                resultsContainer.removeClass('d-none');
            })
            .catch(() => {
                Swal.fire('Error', 'Unable to calculate EMI.', 'error');
            });
    });
});

