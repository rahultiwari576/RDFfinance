$(function () {
    const form = $('#adminLoanApplicationForm');
    let currentStep = 1;

    // Customer Type Toggle
    $('input[name="customer_type"]').on('change', function () {
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

    // Load vehicle companies
    function loadVehicleCompanies() {
        axios.get('/admin/vehicles/companies-list')
            .then(({ data }) => {
                if (data.status && data.companies) {
                    // Populate new vehicle company dropdown
                    const newCompanySelect = $('#vehicleCompanyName');
                    newCompanySelect.empty().append('<option value="">Select Company...</option>');
                    data.companies.forEach(company => {
                        newCompanySelect.append(`<option value="${company.name}">${company.name}</option>`);
                    });

                    // Populate used vehicle company dropdown
                    const usedCompanySelect = $('#usedVehicleCompany');
                    usedCompanySelect.empty().append('<option value="">Select Company...</option>');
                    data.companies.forEach(company => {
                        usedCompanySelect.append(`<option value="${company.name}">${company.name}</option>`);
                    });
                }
            })
            .catch((error) => {
                console.error('Error loading vehicle companies:', error);
            });
    }

    // Load vehicle models by company
    function loadVehicleModels(companyName, targetSelectId, isNewVehicle = true) {
        // Find company ID by name
        axios.get('/admin/vehicles/companies-list')
            .then(({ data }) => {
                if (data.status && data.companies) {
                    const company = data.companies.find(c => c.name === companyName);
                    if (company) {
                        axios.get(`/admin/vehicles/companies/${company.id}/models`)
                            .then(({ data }) => {
                                const targetSelect = $(targetSelectId);
                                targetSelect.empty();

                                if (data.status && data.models && data.models.length > 0) {
                                    targetSelect.append('<option value="">Select Model...</option>');
                                    data.models.forEach(model => {
                                        targetSelect.append(`<option value="${model.name}">${model.name}</option>`);
                                    });
                                    targetSelect.prop('disabled', false);
                                } else {
                                    targetSelect.append('<option value="">No models available</option>');
                                    targetSelect.prop('disabled', false);
                                }
                            })
                            .catch((error) => {
                                console.error('Error loading vehicle models:', error);
                                const targetSelect = $(targetSelectId);
                                targetSelect.empty().append('<option value="">Error loading models</option>');
                            });
                    }
                }
            })
            .catch((error) => {
                console.error('Error finding company:', error);
            });
    }

    // Cascading dropdown for new vehicle
    $('#vehicleCompanyName').on('change', function () {
        const companyName = $(this).val();
        const modelSelect = $('#vehicleModelName');

        if (companyName) {
            loadVehicleModels(companyName, '#vehicleModelName', true);
        } else {
            modelSelect.empty().append('<option value="">Select Company First...</option>').prop('disabled', true);
        }
    });

    // Cascading dropdown for used vehicle
    $('#usedVehicleCompany').on('change', function () {
        const companyName = $(this).val();
        const modelSelect = $('#usedVehicleModel');

        if (companyName) {
            loadVehicleModels(companyName, '#usedVehicleModel', false);
        } else {
            modelSelect.empty().append('<option value="">Select Company First...</option>').prop('disabled', true);
        }
    });

    // Reset form when modal opens
    $('#adminLoanApplicationModal').on('show.bs.modal', function () {
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

        // Load vehicle companies
        loadVehicleCompanies();

        // Reset vehicle model dropdowns
        $('#vehicleModelName, #usedVehicleModel').empty().append('<option value="">Select Company First...</option>').prop('disabled', true);

        // Check for saved draft
        checkForSavedDraft();
    });

    // Function to check and load saved draft
    function checkForSavedDraft() {
        const userId = $('#existingUserId').val() || null;
        if (!userId && $('input[name="customer_type"]:checked').val() === 'new') {
            // For new customers, check by email if available
            const email = $('#customerEmail').val();
            if (!email) return;
        }

        axios.get('/admin/loans/draft/load', {
            params: { user_id: userId }
        })
            .then(({ data }) => {
                if (data.status && data.draft) {
                    Swal.fire({
                        icon: 'info',
                        title: 'Saved Draft Found',
                        html: `A saved draft from ${new Date(data.draft.saved_at).toLocaleString()} was found.<br>Would you like to load it?`,
                        showCancelButton: true,
                        confirmButtonText: 'Yes, Load Draft',
                        cancelButtonText: 'Start Fresh',
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#6c757d'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            loadDraftData(data.draft);
                        }
                    });
                }
            })
            .catch(() => {
                // No draft found or error - continue normally
            });
    }

    // Function to load draft data into form
    function loadDraftData(draft) {
        const formData = draft.form_data;

        // Load customer type
        if (formData.customer_type) {
            $(`input[name="customer_type"][value="${formData.customer_type}"]`).prop('checked', true).trigger('change');
        }

        // Load existing user if applicable
        if (formData.existing_user_id) {
            setTimeout(() => {
                $('#existingUserId').val(formData.existing_user_id).trigger('change');
            }, 500);
        }

        // Load all form fields
        Object.keys(formData).forEach(key => {
            const field = $(`[name="${key}"]`);
            if (field.length) {
                if (field.is(':radio') || field.is(':checkbox')) {
                    field.filter(`[value="${formData[key]}"]`).prop('checked', true);
                } else if (field.is('select')) {
                    field.val(formData[key]).trigger('change');
                } else {
                    field.val(formData[key]);
                }
            }
        });

        // Navigate to saved step
        if (draft.current_step) {
            currentStep = draft.current_step;
            showStep(currentStep);
        }

        Swal.fire({
            icon: 'success',
            title: 'Draft Loaded',
            text: 'Your saved draft has been loaded. You can continue from where you left off.',
            timer: 2000
        });
    }

    // Save draft functionality
    function saveDraft(step) {
        const formData = new FormData(form[0]);
        formData.append('current_step', step);

        // Convert FormData to object (excluding files for now)
        const data = {};
        for (let [key, value] of formData.entries()) {
            if (!(value instanceof File)) {
                data[key] = value;
            }
        }

        axios.post('/admin/loans/draft/save', data)
            .then(({ data }) => {
                if (data.status) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Draft Saved',
                        text: 'Your progress has been saved. You can continue later.',
                        timer: 2000,
                        showConfirmButton: false
                    });
                }
            })
            .catch((error) => {
                Swal.fire({
                    icon: 'error',
                    title: 'Save Failed',
                    text: error.response?.data?.message || 'Failed to save draft.'
                });
            });
    }

    // Save draft buttons - No validation required, save draft without mandatory fields
    $('#saveDraftStep1').on('click', function () {
        saveDraft(1);
    });

    $('#saveDraftStep2').on('click', function () {
        saveDraft(2);
    });

    $('#saveDraftStep3').on('click', function () {
        saveDraft(3);
    });

    $('#saveDraftStep4').on('click', function () {
        saveDraft(4);
    });

    // Load users when modal opens if existing customer is selected
    $('#adminLoanApplicationModal').on('shown.bs.modal', function () {
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
    $('input[name="vehicle_type"]').on('change', function () {
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

    // Step Navigation - No validation required, allow navigation without mandatory fields
    $('#nextToStep2').on('click', function () {
        showStep(2);
    });

    $('#nextToStep3').on('click', function () {
        const step2Result = validateStep2(false);
        if (!step2Result.valid) {
            Swal.fire({
                icon: 'warning',
                title: 'Validation Error',
                text: step2Result.message,
                timer: 3000
            });
            focusOnField(step2Result);
            return;
        }

        const mobile = $('#customerMobile').val() || (window.selectedUserMobile);
        const otp = $('#mobileOTP').val();

        if (!mobile) {
            Swal.fire({
                icon: 'warning',
                title: 'Mobile Required',
                text: 'Please enter customer mobile number in Step 1.'
            });
            showStep(1);
            return;
        }

        $(this).prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Verifying...');

        axios.post('/admin/loans/verify-otp', { mobile: mobile, otp: otp })
            .then(({ data }) => {
                if (data.status) {
                    showStep(3);
                }
            })
            .catch((error) => {
                Swal.fire({
                    icon: 'error',
                    title: 'OTP Verification Failed',
                    text: error.response?.data?.message || 'Invalid or expired OTP.'
                });
            })
            .finally(() => {
                $(this).prop('disabled', false).html('Next <i class="bi bi-arrow-right ms-2"></i>');
            });
    });

    $('#nextToStep4').on('click', function () {
        showStep(4);
    });

    $('#prevToStep1').on('click', function () {
        showStep(1);
    });

    $('#prevToStep2').on('click', function () {
        showStep(2);
    });

    $('#prevToStep3').on('click', function () {
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

    // Enhanced validation function that returns first missing field info
    function validateStep1() {
        const customerType = $('input[name="customer_type"]:checked').val();

        // Validate customer type selection
        if (!customerType) {
            return {
                valid: false,
                step: 1,
                field: $('input[name="customer_type"]').first(),
                message: 'Please select customer type (New or Existing).'
            };
        }

        if (customerType === 'existing') {
            const existingUserId = $('#existingUserId');
            if (!existingUserId.val()) {
                return {
                    valid: false,
                    step: 1,
                    field: existingUserId,
                    message: 'Please select a customer.'
                };
            }
        } else {
            // Validate new customer fields
            const customerFields = [
                { id: 'customer_first_name', name: 'Customer First Name' },
                { id: 'customer_last_name', name: 'Customer Last Name' },
                { id: 'customer_password', name: 'Customer Password' },
                { id: 'customer_aadhar_number', name: 'Customer Aadhar Number' },
                { id: 'customer_mobile_number', name: 'Customer Mobile Number' },
                { id: 'customer_email', name: 'Customer Email' },
                { id: 'customer_pan_number', name: 'Customer PAN Number' },
                { id: 'customer_address_type', name: 'Customer Address Type' },
                { id: 'customer_employment_type', name: 'Customer Employment Type' }
            ];

            for (let field of customerFields) {
                const fieldElement = $(`#${field.id}`);
                if (!fieldElement.length) {
                    console.error(`Customer field not found: #${field.id}`);
                    continue;
                }

                const fieldValue = fieldElement.val();
                if (!fieldValue || fieldValue.trim() === '') {
                    return {
                        valid: false,
                        step: 1,
                        field: fieldElement,
                        message: `Please fill ${field.name}.`
                    };
                }
            }
        }

        // Validate guarantor (always required)
        const guarantorFields = [
            { id: 'guarantorFirstName', name: 'Guarantor First Name' },
            { id: 'guarantorLastName', name: 'Guarantor Last Name' },
            { id: 'guarantorAadhar', name: 'Guarantor Aadhar Number' },
            { id: 'guarantorPAN', name: 'Guarantor PAN Number' },
            { id: 'guarantorMobile', name: 'Guarantor Mobile Number' }
        ];

        for (let field of guarantorFields) {
            const fieldElement = $(`#${field.id}`);
            if (!fieldElement.length) {
                console.error(`Guarantor field not found: #${field.id}`);
                return {
                    valid: false,
                    step: 1,
                    field: null,
                    message: `Guarantor field "${field.name}" not found. Please refresh the page.`
                };
            }

            const fieldValue = fieldElement.val();
            if (!fieldValue || fieldValue.trim() === '') {
                return {
                    valid: false,
                    step: 1,
                    field: fieldElement,
                    message: `Please fill ${field.name}.`
                };
            }
        }

        return { valid: true };
    }

    function validateStep2(isFinal = false) {
        // Validate vehicle type selection
        const vehicleType = $('input[name="vehicle_type"]:checked').val();
        if (!vehicleType) {
            return {
                valid: false,
                step: 2,
                field: $('input[name="vehicle_type"]').first(),
                message: 'Please select vehicle type (New or Used).'
            };
        }

        if (isFinal) {
            if (vehicleType === 'new') {
                // Validate company selection
                const companySelect = $('#vehicleCompanyName');
                if (!companySelect.length || !companySelect.val() || companySelect.val().trim() === '') {
                    return {
                        valid: false,
                        step: 2,
                        field: companySelect,
                        message: 'Please select a vehicle company.'
                    };
                }

                // Validate model selection
                const modelSelect = $('#vehicleModelName');
                if (!modelSelect.length || !modelSelect.val() || modelSelect.val().trim() === '') {
                    return {
                        valid: false,
                        step: 2,
                        field: modelSelect,
                        message: 'Please select a vehicle model.'
                    };
                }
            } else {
                // Validate used vehicle company selection
                const usedCompanySelect = $('#usedVehicleCompany');
                if (!usedCompanySelect.length || !usedCompanySelect.val() || usedCompanySelect.val().trim() === '') {
                    return {
                        valid: false,
                        step: 2,
                        field: usedCompanySelect,
                        message: 'Please select a vehicle company.'
                    };
                }

                // Validate used vehicle model selection
                const usedModelSelect = $('#usedVehicleModel');
                if (!usedModelSelect.length || !usedModelSelect.val() || usedModelSelect.val().trim() === '') {
                    return {
                        valid: false,
                        step: 2,
                        field: usedModelSelect,
                        message: 'Please select a vehicle model.'
                    };
                }

                // Validate other used vehicle fields
                const usedFields = [
                    { id: 'engineNumber', name: 'Engine Number' },
                    { id: 'chassisNumber', name: 'Chassis Number' },
                    { id: 'registrationNumber', name: 'Registration Number' },
                    { id: 'registrationDate', name: 'Registration Date' },
                    { id: 'registrationValidity', name: 'Registration Validity' },
                    { id: 'ownerName', name: 'Owner Name' },
                    { id: 'vehicleColor', name: 'Vehicle Color' }
                ];

                for (let field of usedFields) {
                    const fieldElement = $(`#${field.id}`);
                    if (!fieldElement.length) {
                        console.error(`Used vehicle field not found: #${field.id}`);
                        continue;
                    }

                    if (!fieldElement.val() || fieldElement.val().trim() === '') {
                        return {
                            valid: false,
                            step: 2,
                            field: fieldElement,
                            message: `Please fill ${field.name}.`
                        };
                    }
                }
            }
        }

        // Validate mobile OTP
        const mobileOTP = $('#mobileOTP');
        if (!mobileOTP.length || !mobileOTP.val() || mobileOTP.val().trim() === '') {
            return {
                valid: false,
                step: 2,
                field: mobileOTP,
                message: 'Please enter mobile OTP.'
            };
        }

        // Note: For real-time verification, we could call verifyLoanOtp here,
        // but since this is a synchronous validation function, we'll handle
        // the async verification in the button click handler.

        return { valid: true };
    }

    function validateStep3() {
        // Validate Aadhar OTP
        const aadharOTP = $('#aadharOTP');
        if (!aadharOTP.length || !aadharOTP.val() || aadharOTP.val().trim() === '') {
            return {
                valid: false,
                step: 3,
                field: aadharOTP,
                message: 'Please enter Aadhar OTP.'
            };
        }

        // Validate loan and bank details
        const requiredFields = [
            { id: 'loanAmount', name: 'Loan Amount' },
            { id: 'loanTenure', name: 'Loan Tenure' },
            { id: 'emiCount', name: 'EMI Count' },
            { id: 'interestRate', name: 'Interest Rate' },
            { id: 'bankAccountNumber', name: 'Bank Account Number' },
            { id: 'bankAccountName', name: 'Bank Account Name' },
            { id: 'bankIFSC', name: 'Bank IFSC Code' }
        ];

        for (let field of requiredFields) {
            const fieldElement = $(`#${field.id}`);
            if (!fieldElement.length) {
                console.error(`Field not found: #${field.id}`);
                continue;
            }

            if (!fieldElement.val() || fieldElement.val().trim() === '') {
                return {
                    valid: false,
                    step: 3,
                    field: fieldElement,
                    message: `Please fill ${field.name}.`
                };
            }
        }

        return { valid: true };
    }

    function validateStep4() {
        // Validate required documents
        const vehicleType = $('input[name="vehicle_type"]:checked').val();

        // Validate vehicle-specific document
        if (vehicleType === 'new') {
            const taxInvoice = $('#taxInvoice');
            if (!taxInvoice.length) {
                return {
                    valid: false,
                    step: 4,
                    field: null,
                    message: 'Tax Invoice field not found. Please refresh the page.'
                };
            }

            const taxInvoiceFile = taxInvoice[0];
            if (!taxInvoiceFile || !taxInvoiceFile.files || taxInvoiceFile.files.length === 0) {
                return {
                    valid: false,
                    step: 4,
                    field: taxInvoice,
                    message: 'Please upload Tax Invoice.'
                };
            }
        } else {
            const rcDocument = $('#rcDocument');
            if (!rcDocument.length) {
                return {
                    valid: false,
                    step: 4,
                    field: null,
                    message: 'RC Document field not found. Please refresh the page.'
                };
            }

            const rcDocumentFile = rcDocument[0];
            if (!rcDocumentFile || !rcDocumentFile.files || rcDocumentFile.files.length === 0) {
                return {
                    valid: false,
                    step: 4,
                    field: rcDocument,
                    message: 'Please upload RC Document.'
                };
            }
        }

        // Validate all required documents
        const requiredDocs = [
            { id: 'insurance', name: 'Insurance Document' },
            { id: 'deliveryPhoto', name: 'Delivery Photo' },
            { id: 'aadharCard', name: 'Aadhar Card' },
            { id: 'panCard', name: 'PAN Card' },
            { id: 'addressProof', name: 'Address Proof' },
            { id: 'rtoBooklet', name: 'RTO Booklet' },
            { id: 'cheque1', name: 'Cheque 1' },
            { id: 'cheque2', name: 'Cheque 2' },
            { id: 'cheque3', name: 'Cheque 3' },
            { id: 'guarantorAadharDoc', name: 'Guarantor Aadhar Document' },
            { id: 'guarantorPanDoc', name: 'Guarantor PAN Document' },
            { id: 'guarantorCheque', name: 'Guarantor Cheque' }
        ];

        for (let doc of requiredDocs) {
            const docElement = $(`#${doc.id}`);
            if (!docElement.length) {
                return {
                    valid: false,
                    step: 4,
                    field: null,
                    message: `Document field "${doc.name}" not found. Please refresh the page.`
                };
            }

            const fileInput = docElement[0];
            if (!fileInput.files || fileInput.files.length === 0) {
                return {
                    valid: false,
                    step: 4,
                    field: docElement,
                    message: `Please upload ${doc.name}.`
                };
            }
        }

        return { valid: true };
    }

    // Helper function to focus on a field and show error
    function focusOnField(validationResult) {
        // Navigate to the step first
        showStep(validationResult.step);

        if (validationResult.field && validationResult.field.length) {
            // Wait for step to be visible, then scroll and focus
            setTimeout(() => {
                const field = validationResult.field;
                const offset = field.offset();

                if (offset) {
                    // Scroll to field
                    $('html, body').animate({
                        scrollTop: offset.top - 150
                    }, 500, function () {
                        // Highlight the field
                        field.addClass('is-invalid');

                        // For file inputs, we can't focus, but we can click the label
                        if (field.is('input[type="file"]')) {
                            // Try to find and click the associated label
                            const label = field.closest('.col-md-6, .col-md-4').find('label');
                            if (label.length) {
                                label.css('color', '#dc3545');
                                setTimeout(() => {
                                    label.css('color', '');
                                }, 3000);
                            }
                        } else {
                            // Focus on the field (for text inputs, selects, etc.)
                            field.focus();
                        }

                        // Remove highlight after 3 seconds
                        setTimeout(() => {
                            field.removeClass('is-invalid');
                        }, 3000);
                    });
                } else {
                    // If offset is not available, just highlight
                    field.addClass('is-invalid');
                    if (!field.is('input[type="file"]')) {
                        field.focus();
                    }
                    setTimeout(() => {
                        field.removeClass('is-invalid');
                    }, 3000);
                }
            }, 300);
        }
    }

    // Comprehensive validation for submit - validates all steps and focuses on first missing field
    function validateAllSteps() {
        // Validate Step 1
        const step1Result = validateStep1();
        if (!step1Result.valid) {
            Swal.fire({
                icon: 'warning',
                title: 'Validation Error',
                text: step1Result.message,
                timer: 3000,
                showConfirmButton: true
            });
            focusOnField(step1Result);
            return false;
        }

        // Validate Step 2 (Full validation for final submission)
        const step2Result = validateStep2(true);
        if (!step2Result.valid) {
            Swal.fire({
                icon: 'warning',
                title: 'Validation Error',
                text: step2Result.message,
                timer: 3000,
                showConfirmButton: true
            });
            focusOnField(step2Result);
            return false;
        }

        // Validate Step 3
        const step3Result = validateStep3();
        if (!step3Result.valid) {
            Swal.fire({
                icon: 'warning',
                title: 'Validation Error',
                text: step3Result.message,
                timer: 3000,
                showConfirmButton: true
            });
            focusOnField(step3Result);
            return false;
        }

        // Validate Step 4
        const step4Result = validateStep4();
        if (!step4Result.valid) {
            Swal.fire({
                icon: 'warning',
                title: 'Validation Error',
                text: step4Result.message,
                timer: 3000,
                showConfirmButton: true
            });
            focusOnField(step4Result);
            return false;
        }

        return true;
    }

    // CIBIL Check (Placeholder - integrate with actual API)
    $('#checkCIBIL').on('click', function () {
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
    $('#existingUserId').on('change', function () {
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
    $(document).on('click', '.apply-loan-user', function () {
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

    // Form submission - Validate all steps before submitting
    form.on('submit', function (e) {
        e.preventDefault();

        // Validate all steps before submission
        if (!validateAllSteps()) {
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
                    // Delete draft after successful submission
                    const userId = $('#existingUserId').val() || null;
                    if (userId) {
                        axios.delete('/admin/loans/draft/delete', {
                            params: { user_id: userId }
                        }).catch(() => { });
                    }

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
    $('#customerAadhar, #guarantorAadhar').on('input', function () {
        this.value = this.value.replace(/\D/g, '').slice(0, 12);
    });

    $('#customerMobile, #customerAltMobile, #guarantorMobile, #reference1Mobile, #reference2Mobile').on('input', function () {
        this.value = this.value.replace(/\D/g, '').slice(0, 10);
    });

    $('#customerPAN, #guarantorPAN').on('input', function () {
        this.value = this.value.toUpperCase().replace(/[^A-Z0-9]/g, '').slice(0, 10);
    });

    // Mobile OTP Logic
    let lastSentMobile = '';
    $('#customerMobile').on('input', function () {
        const mobile = $(this).val();
        if (mobile.length === 10 && mobile !== lastSentMobile) {
            lastSentMobile = mobile;
            sendLoanOtp(mobile);
        }
    });

    // Also handle existing user selection for OTP
    $('#existingUserId').on('change', function () {
        const userId = $(this).val();
        const userData = usersDataMap[userId];
        if (userData && userData.phone_number) {
            window.selectedUserMobile = userData.phone_number;
            // For existing users, we'll send OTP automatically when selected
            if (window.selectedUserMobile.length === 10) {
                sendLoanOtp(window.selectedUserMobile);
            }
        }
    });

    function sendLoanOtp(mobile) {
        Swal.fire({
            title: 'Sending OTP...',
            text: `Sending OTP to mobile number ${mobile}`,
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        axios.post('/admin/loans/send-otp', { mobile: mobile })
            .then(({ data }) => {
                if (data.status) {
                    Swal.fire({
                        icon: 'success',
                        title: 'OTP Sent',
                        text: data.message,
                        timer: 4000
                    });
                }
            })
            .catch((error) => {
                Swal.fire({
                    icon: 'error',
                    title: 'Failed to send OTP',
                    text: error.response?.data?.message || 'Something went wrong while sending OTP.'
                });
                lastSentMobile = ''; // Allow retry
            });
    }
});

