import axios from 'axios';
import Swal from 'sweetalert2';

$(function () {
    $('#emailLoginForm').on('submit', function (e) {
        e.preventDefault();

        const form = $(this);

        axios.post(form.attr('action'), form.serialize())
            .then(({ data }) => {
                Swal.fire('Success', 'Logged in successfully', 'success').then(() => {
                    window.location.href = data.redirect;
                });
            })
            .catch((error) => {
                const message = error.response?.data?.message || 'Unable to login';
                Swal.fire('Error', message, 'error');
            });
    });

    $('#aadharLoginForm').on('submit', function (e) {
        e.preventDefault();
        const form = $(this);

        axios.post(form.attr('action'), form.serialize())
            .then(({ data }) => {
                Swal.fire('OTP Sent', data.message, 'success');
                $('#otp_id').val(data.otp_token);
                $('#otpVerificationForm').removeClass('d-none');
            })
            .catch((error) => {
                const message = error.response?.data?.message || 'Unable to send OTP';
                Swal.fire('Error', message, 'error');
            });
    });

    $('#otpVerificationForm').on('submit', function (e) {
        e.preventDefault();
        const formData = $(this).serialize();

        axios.post($(this).attr('action'), formData)
            .then(({ data }) => {
                Swal.fire('Success', data.message, 'success').then(() => {
                    window.location.href = data.redirect;
                });
            })
            .catch((error) => {
                const message = error.response?.data?.message || 'Failed to verify OTP';
                Swal.fire('Error', message, 'error');
            });
    });
});

