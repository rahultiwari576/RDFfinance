import axios from 'axios';
import Swal from 'sweetalert2';

$(function () {
    const form = $('#registrationForm');
    const extractButton = $('#extractAadharButton');

    extractButton.on('click', function () {
        const fileInput = $('#aadhar_document')[0];

        if (!fileInput.files.length) {
            Swal.fire('Missing File', 'Please upload an Aadhar PDF or image first.', 'warning');
            return;
        }

        const formData = new FormData();
        formData.append('aadhar_document', fileInput.files[0]);

        extractButton.prop('disabled', true);

        axios.post(form.data('extractUrl'), formData, {
            headers: { 'Content-Type': 'multipart/form-data' },
        }).then(({ data }) => {
            $('#aadhar_number_field').val(data.aadhar_number);
            Swal.fire('Success', 'Aadhar number extracted successfully.', 'success');
        }).catch((error) => {
            const message = error.response?.data?.message || 'Failed to extract Aadhar number.';
            Swal.fire('Error', message, 'error');
        }).finally(() => {
            extractButton.prop('disabled', false);
        });
    });

    form.on('submit', function (e) {
        e.preventDefault();

        const formData = new FormData(this);

        axios.post(form.attr('action'), formData, {
            headers: { 'Content-Type': 'multipart/form-data' },
        }).then(({ data }) => {
            Swal.fire('Success', data.message, 'success').then(() => {
                window.location.href = data.redirect;
            });
        }).catch((error) => {
            if (error.response?.status === 422) {
                const validationErrors = Object.values(error.response.data.errors || {}).flat().join('<br>');
                Swal.fire({
                    icon: 'error',
                    title: 'Validation Failed',
                    html: validationErrors || 'Please check your input.',
                });
            } else {
                const message = error.response?.data?.message || 'Registration failed.';
                Swal.fire('Error', message, 'error');
            }
        });
    });
});

