$(function() {
    const form = $('#adminLoanApplicationForm');
    let currentStep = 1;

    // Customer Type Toggle
    $('input[name="customer_type"]').on('change', function() {
        if ($(this).val() === 'existing') {
            $('#existingCustomerSection').removeClass('d-none');
            // Hide only customer input fields row (not guarantor section)
            $('#customerFieldsRow').addClass('d-none');
            $('#customerFieldsRow input, #customerFieldsRow select').prop('required', false);
            loadExistingUsers();
        } else {
            $('#existingCustomerSection').addClass('d-none');
            // Show customer input fields
            $('#customerFieldsRow').removeClass('d-none');
            $('#customerFieldsRow input[required], #customerFieldsRow select[required]').prop('required', true);
        }
    });
    
    // Reset form when modal opens
    $('#adminLoanApplicationModal').on('show.bs.modal', function() {
        // Reset to step 1
        currentStep = 1;
        showStep(1);
        
        // Reset form
        form[0].reset();
        
        // Reset customer type to new (unless coming from apply loan button)
        if (!window.preSelectUserId) {
            $('#newCustomer').prop('checked', true).trigger('change');
        }
        
        // Clear any notifications
        $('.user-selected-notification').remove();
        
        // Ensure guarantor section is always visible
        $('#guarantorSection').removeClass('d-none');
        $('#customerFieldsRow').removeClass('d-none');
    });
    
    // Load users when modal opens if existing customer is selected
    $('#adminLoanApplicationModal').on('shown.bs.modal', function() {
        if ($('input[name="customer_type"]:checked').val() === 'existing') {
            loadExistingUsers();
            // If user was pre-selected, select them
            if (window.preSelectUserId) {
                setTimeout(() => {
                    $('#existingUserId').val(window.preSelectUserId).trigger('change');
                    window.preSelectUserId = null; // Clear flag
                }, 300);
            }
        }
    });

    // Vehicle Type Toggle
    $('input[name="vehicle_type"]').on('change', function() {
        if ($(this).val() === 'used') {
            $('#newVehicleFields').addClass('d-none').find('input').prop('required', false);
            $('#usedVehicleFields').removeClass('d-none').find('input[required]').prop('required', true);
            $('#rcDocumentField').removeClass('d-none');
            $('#taxInvoice').closest('.col-md-6').addClass('d-none');
            $('#rcDocument').prop('required', true);
            $('#taxInvoice').prop('required', false);
        } else {
            $('#newVehicleFields').removeClass('d-none').find('input').prop('required', true);
            $('#usedVehicleFields').addClass('d-none').find('input').prop('required', false);
            $('#rcDocumentField').addClass('d-none');
            $('#taxInvoice').closest('.col-md-6').removeClass('d-none');
            $('#rcDocument').prop('required', false);
            $('#taxInvoice').prop('required', true);
        }
    });

    // Step Navigation
    $('#nextToStep2').on('click', function() {
        if (validateStep1()) {
            showStep(2);
        }
    });

    $('#nextToStep3').on('click', function() {
        if (validateStep2()) {
            showStep(3);
        }
    });

    $('#nextToStep4').on('click', function() {
        if (validateStep3()) {
            showStep(4);
        }
    });

    $('#prevToStep1').on('click', function() {
        showStep(1);
    });

    $('#prevToStep2').on('click', function() {
        showStep(2);
    });

    $('#prevToStep3').on('click', function() {
        showStep(3);
    });

    function showStep(step) {
        currentStep = step;
        $('.form-step').addClass('d-none').removeClass('active');
        $(`#step${step}`).removeClass('d-none').addClass('active');
        updateProgress();
    }

    function updateProgress() {
        $('.step').removeClass('active');
        $(`.step[data-step="${currentStep}"]`).addClass('active');
    }

    function validateStep1() {
        const customerType = $('input[name="customer_type"]:checked').val();
        if (customerType === 'existing') {
            if (!$('#existingUserId').val()) {
                Swal.fire({ icon: 'warning', title: 'Required', text: 'Please select a customer.' });
                return false;
            }
        } else {
            const required = ['customer_first_name', 'customer_last_name', 'customer_password', 
                             'customer_aadhar_number', 'customer_mobile_number', 'customer_email', 
                             'customer_pan_number', 'customer_address_type', 'customer_employment_type'];
            for (let field of required) {
                if (!$(`#${field}`).val()) {
                    Swal.fire({ icon: 'warning', title: 'Required', text: 'Please fill all required fields.' });
                    return false;
                }
            }
        }
        
        // Validate guarantor (always required)
        const guarantorFields = [
            { id: 'guarantorFirstName', name: 'First Name' },
            { id: 'guarantorLastName', name: 'Last Name' },
            { id: 'guarantorAadhar', name: 'Aadhar Number' },
            { id: 'guarantorPAN', name: 'PAN Number' },
            { id: 'guarantorMobile', name: 'Mobile Number' }
        ];
        
        for (let field of guarantorFields) {
            const fieldElement = $(`#${field.id}`);
            const fieldValue = fieldElement.val();
            
            // Check if field exists and has a value
            if (!fieldElement.length) {
                console.error(`Guarantor field not found: #${field.id}`);
                Swal.fire({ 
                    icon: 'error', 
                    title: 'Form Error', 
                    text: `Guarantor field "${field.name}" not found. Please refresh the page.` 
                });
                return false;
            }
            
            if (!fieldValue || fieldValue.trim() === '') {
                Swal.fire({ 
                    icon: 'warning', 
                    title: 'Guarantor Details Required', 
                    text: `Please fill ${field.name}.` 
                });
                // Scroll to the field
                $('html, body').animate({
                    scrollTop: fieldElement.offset().top - 100
                }, 500);
                fieldElement.focus();
                return false;
            }
        }
        return true;
    }

    function validateStep2() {
        const vehicleType = $('input[name="vehicle_type"]:checked').val();
        if (vehicleType === 'new') {
            if (!$('#vehicleCompanyName').val() || !$('#vehicleModelName').val()) {
                Swal.fire({ icon: 'warning', title: 'Required', text: 'Please fill vehicle details.' });
                return false;
            }
        } else {
            const usedFields = ['usedVehicleCompany', 'usedVehicleModel', 'engineNumber', 'chassisNumber', 
                               'registrationNumber', 'registrationDate', 'registrationValidity', 'ownerName', 'vehicleColor'];
            for (let field of usedFields) {
                if (!$(`#${field}`).val()) {
                    Swal.fire({ icon: 'warning', title: 'Required', text: 'Please fill all RC details.' });
                    return false;
                }
            }
        }
        
        if (!$('#mobileOTP').val()) {
            Swal.fire({ icon: 'warning', title: 'Required', text: 'Please enter mobile OTP.' });
            return false;
        }
        
        // References are optional - no validation needed
        
        return true;
    }

    function validateStep3() {
        if (!$('#aadharOTP').val()) {
            Swal.fire({ icon: 'warning', title: 'Required', text: 'Please enter Aadhar OTP.' });
            return false;
        }
        
        const required = ['loanAmount', 'loanTenure', 'emiCount', 'interestRate', 
                          'bankAccountNumber', 'bankAccountName', 'bankIFSC'];
        for (let field of required) {
            if (!$(`#${field}`).val()) {
                Swal.fire({ icon: 'warning', title: 'Required', text: 'Please fill all required fields.' });
                return false;
            }
        }
        return true;
    }

    // CIBIL Check (Placeholder - integrate with actual API)
    $('#checkCIBIL').on('click', function() {
        const pan = $('#customerPAN').val();
        if (!pan) {
            Swal.fire({ icon: 'warning', title: 'Required', text: 'Please enter PAN number first.' });
            return;
        }
        
        $(this).prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Checking...');
        
        // TODO: Integrate with actual CIBIL API
        setTimeout(() => {
            $('#cibilScore').val('750');
            $('#cibilDetails').val(JSON.stringify({ score: 750, status: 'Good' }));
            $('#cibilResult').html('<div class="alert alert-success">CIBIL Score: 750 - Good</div>');
            $('#checkCIBIL').prop('disabled', false).html('<i class="bi bi-search me-2"></i>Check CIBIL Score');
        }, 2000);
    });

    // Store users data globally
    let usersDataMap = {};
    
    // Load existing users
    function loadExistingUsers() {
        axios.get('/admin/users')
            .then(({ data }) => {
                const select = $('#existingUserId');
                select.empty().append('<option value="">Select Customer...</option>');
                usersDataMap = {}; // Reset map
                
                if (data.users && data.users.length > 0) {
                    data.users.forEach(user => {
                        // Only show non-admin users
                        if (user.role !== 'admin') {
                            usersDataMap[user.id] = user;
                            select.append(`<option value="${user.id}">${user.name} (${user.email}) - Aadhar: ${user.aadhar_number || 'N/A'}</option>`);
                        }
                    });
                } else {
                    select.append('<option value="" disabled>No users found</option>');
                }
            })
            .catch((error) => {
                console.error('Error loading users:', error);
                Swal.fire({ icon: 'error', title: 'Error', text: 'Failed to load users. Please refresh the page.' });
            });
    }
    
    // Populate form when user is selected
    $('#existingUserId').on('change', function() {
        const userId = $(this).val();
        const userData = usersDataMap[userId];
        
        if (userData && userData.id) {
            // Show confirmation that user is selected
            console.log('Selected user:', userData);
            // Remove any existing notifications
            $('.user-selected-notification').remove();
            // Show notification
            const notification = $(`<div class="alert alert-info alert-dismissible fade show user-selected-notification" role="alert" style="margin-top: 10px;">
                <strong><i class="bi bi-check-circle me-2"></i>User Selected:</strong> ${userData.name} (${userData.email})
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>`);
            $('#existingCustomerSection').after(notification);
            setTimeout(() => notification.fadeOut(() => notification.remove()), 5000);
        }
    });
    
    // Handle "Apply Loan" button from users table
    $(document).on('click', '.apply-loan-user', function() {
        const userId = $(this).data('user-id');
        const userName = $(this).data('user-name');
        const userEmail = $(this).data('user-email');
        
        // Set flag to pre-select user
        window.preSelectUserId = userId;
        
        // Open the loan application modal
        $('#adminLoanApplicationModal').modal('show');
        
        // Set to existing customer
        $('#existingCustomer').prop('checked', true).trigger('change');
    });

    // Form submission
    form.on('submit', function(e) {
        e.preventDefault();
        
        if (!validateStep3()) {
            showStep(3);
            return;
        }
        
        const submitBtn = $('#submitLoanApplication');
        const originalText = submitBtn.html();
        submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Submitting...');
        
        const formData = new FormData(this);
        
        axios.post(form.attr('action'), formData, {
            headers: { 'Content-Type': 'multipart/form-data' },
        })
        .then(({ data }) => {
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: data.message || 'Loan application submitted successfully.',
                timer: 3000
            }).then(() => {
                $('#adminLoanApplicationModal').modal('hide');
                form[0].reset();
                currentStep = 1;
                showStep(1);
                location.reload();
            });
        })
        .catch((error) => {
            let message = 'Loan application failed.';
            if (error.response?.data?.message) {
                message = error.response.data.message;
            } else if (error.response?.data?.errors) {
                const errors = Object.values(error.response.data.errors).flat();
                message = errors.join('<br>');
            }
            Swal.fire({
                icon: 'error',
                title: 'Application Failed',
                html: message
            });
        })
        .finally(() => {
            submitBtn.prop('disabled', false).html(originalText);
        });
    });

    // Format inputs
    $('#customerAadhar, #guarantorAadhar').on('input', function() {
        this.value = this.value.replace(/\D/g, '').slice(0, 12);
    });
    
    $('#customerMobile, #customerAltMobile, #guarantorMobile, #reference1Mobile, #reference2Mobile').on('input', function() {
        this.value = this.value.replace(/\D/g, '').slice(0, 10);
    });
    
    $('#customerPAN, #guarantorPAN').on('input', function() {
        this.value = this.value.toUpperCase().replace(/[^A-Z0-9]/g, '').slice(0, 10);
    });
});

