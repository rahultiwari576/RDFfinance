require('./bootstrap');

window.Swal = require('sweetalert2');
window.$ = window.jQuery = require('jquery');

import 'bootstrap';

$(function () {
    $('#logoutButton').on('click', function () {
        axios.post($(this).data('url') ?? '/logout')
            .then(({ data }) => {
                window.location.href = data.redirect;
            })
            .catch(() => Swal.fire('Error', 'Failed to logout.', 'error'));
    });
});

