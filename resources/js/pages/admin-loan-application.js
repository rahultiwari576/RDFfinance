$(function () {
    const form = $('#adminLoanApplicationForm');
    let currentStep = 1;

    const statesAndCities = {
        "Andhra Pradesh": ["Visakhapatnam", "Vijayawada", "Guntur", "Nellore", "Kurnool", "Tirupati", "Rajahmundry", "Kakinada"],
        "Arunachal Pradesh": ["Itanagar", "Naharlagun", "Pasighat"],
        "Assam": ["Guwahati", "Silchar", "Dibrugarh", "Jorhat", "Nagaon", "Tinsukia", "Tezpur"],
        "Bihar": ["Patna", "Gaya", "Bhagalpur", "Muzaffarpur", "Purnia", "Darbhanga", "Arrah", "Begusarai"],
        "Chhattisgarh": ["Raipur", "Bhilai", "Bilaspur", "Korba", "Rajnandgaon", "Raigarh", "Jagdalpur"],
        "Goa": ["Panaji", "Margao", "Vasco da Gama", "Mapusa"],
        "Gujarat": ["Ahmedabad", "Surat", "Vadodara", "Rajkot", "Bhavnagar", "Jamnagar", "Junagadh", "Gandhinagar"],
        "Haryana": ["Faridabad", "Gurgaon", "Panipat", "Ambala", "Yamunanagar", "Rohtak", "Hisar", "Karnal"],
        "Himachal Pradesh": ["Shimla", "Dharamshala", "Solan", "Mandi"],
        "Jharkhand": ["Jamshedpur", "Dhanbad", "Ranchi", "Bokaro", "Deoghar", "Phusro", "Hazaribagh"],
        "Karnataka": ["Bangalore", "Hubli", "Mysore", "Gulbarga", "Belgaum", "Mangalore", "Davanagere", "Bellary"],
        "Kerala": ["Thiruvananthapuram", "Kochi", "Kozhikode", "Kollam", "Thrissur", "Alappuzha", "Palakkad"],
        "Madhya Pradesh": ["Indore", "Bhopal", "Jabalpur", "Gwalior", "Ujjain", "Sagar", "Dewas", "Satna"],
        "Maharashtra": ["Mumbai", "Pune", "Nagpur", "Thane", "Pimpri-Chinchwad", "Nashik", "Kalyan-Dombivli", "Vasai-Virar", "Aurangabad", "Navi Mumbai", "Solapur", "Mira-Bhayandar", "Nagpur"],
        "Manipur": ["Imphal"],
        "Meghalaya": ["Shillong", "Tura"],
        "Mizoram": ["Aizawl", "Lunglei"],
        "Nagaland": ["Dimapur", "Kohima"],
        "Odisha": ["Bhubaneswar", "Cuttack", "Rourkela", "Berhampur", "Sambalpur", "Puri", "Balasore"],
        "Punjab": ["Ludhiana", "Amritsar", "Jalandhar", "Patiala", "Bathinda", "Mohali", "Hoshiarpur"],
        "Rajasthan": ["Jaipur", "Jodhpur", "Kota", "Bikaner", "Ajmer", "Udaipur", "Bhilwara", "Alwar"],
        "Sikkim": ["Gangtok"],
        "Tamil Nadu": ["Chennai", "Coimbatore", "Madurai", "Tiruchirappalli", "Salem", "Tiruppur", "Erode", "Vellore"],
        "Telangana": ["Hyderabad", "Warangal", "Nizamabad", "Khammam", "Karimnagar", "Ramagundam", "Mahbubnagar"],
        "Tripura": ["Agartala"],
        "Uttar Pradesh": ["Lucknow", "Kanpur", "Ghaziabad", "Agra", "Meerut", "Varanasi", "Prayagraj", "Bareilly", "Aligarh", "Moradabad", "Noida", "Gorakhpur"],
        "Uttarakhand": ["Dehradun", "Haridwar", "Roorkee", "Haldwani", "Rudrapur"],
        "West Bengal": ["Kolkata", "Howrah", "Asansol", "Siliguri", "Durgapur", "Bardhaman", "Malda", "Baharampur"],
        "Andaman and Nicobar Islands": ["Port Blair"],
        "Chandigarh": ["Chandigarh"],
        "Dadra and Nagar Haveli and Daman and Diu": ["Daman", "Diu", "Silvassa"],
        "Delhi": ["Delhi", "New Delhi"],
        "Jammu and Kashmir": ["Srinagar", "Jammu", "Anantnag"],
        "Ladakh": ["Leh", "Kargil"],
        "Lakshadweep": ["Kavaratti"],
        "Puducherry": ["Puducherry", "Ozhukarai"]
    };

    // Initialize States
    function initializeStates() {
        const stateSelect = $('#customerState');
        if (stateSelect.length) {
            stateSelect.empty().append('<option value="">Select State...</option>');
            Object.keys(statesAndCities).sort().forEach(state => {
                stateSelect.append(`<option value="${state}">${state}</option>`);
            });
        }
    }

    // Populate Cities based on State
    $('#customerState').on('change', function () {
        const state = $(this).val();
        const citySelect = $('#customerCity');
        citySelect.empty().append('<option value="">Select City...</option>');

        if (state && statesAndCities[state]) {
            statesAndCities[state].sort().forEach(city => {
                citySelect.append(`<option value="${city}">${city}</option>`);
            });
            citySelect.prop('disabled', false);
        } else {
            citySelect.prop('disabled', true);
        }
    });

    // Address Type Change Handler
    $('#customerAddressType').on('change', function () {
        if ($(this).val()) {
            $('#addressDetailsContainer').removeClass('d-none').hide().fadeIn();
            initializeStates();
        } else {
            $('#addressDetailsContainer').fadeOut();
        }
    });

    // Pincode Formatting
    $('#customerPincode').on('input', function () {
        this.value = this.value.replace(/\D/g, '').slice(0, 6);
    });

    // Customer Type Toggle
    $('input[name="customer_type"]').on('change', function () {
        if ($(this).val() === 'existing') {
            $('#existingCustomerSection').removeClass('d-none');
            // Show customer fields so they can be pre-filled and edited
            $('#customerFieldsRow').removeClass('d-none');
            // Password is not required for existing users
            $('#customerPassword').prop('required', false).closest('.col-md-6').fadeOut();
            loadExistingUsers();
        } else {
            $('#existingCustomerSection').addClass('d-none');
            // Show and reset customer fields
            $('#customerFieldsRow').removeClass('d-none');
            $('#customerFieldsRow input, #customerFieldsRow select').val('').trigger('change');
            $('#customerFieldsRow input[required], #customerFieldsRow select[required]').prop('required', true);
            $('#customerPassword').prop('required', true).closest('.col-md-6').fadeIn();
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

        // OTP verification bypassed for now
        showStep(3);
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
        }

        // Validate common customer fields (required for both)
        const customerFields = [
            { id: 'customerFirstName', name: 'Customer First Name' },
            { id: 'customerLastName', name: 'Customer Last Name' },
            { id: 'customerAadhar', name: 'Customer Aadhar Number' },
            { id: 'customerMobile', name: 'Customer Mobile Number' },
            { id: 'customerEmail', name: 'Customer Email' },
            { id: 'customerPAN', name: 'Customer PAN Number' },
            { id: 'customerAddressType', name: 'Customer Address Type' },
            { id: 'customerEmploymentType', name: 'Customer Employment Type' }
        ];

        // Password is only required for new customers
        if (customerType === 'new') {
            customerFields.push({ id: 'customerPassword', name: 'Customer Password' });
        }

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

        // Validate Address Details if Address Type is selected
        if ($('#customerAddressType').val()) {
            const addressFields = [
                { id: 'customerFlatBuilding', name: 'Flat / Building' },
                { id: 'customerLocality', name: 'Locality' },
                { id: 'customerState', name: 'State' },
                { id: 'customerCity', name: 'City' },
                { id: 'customerPincode', name: 'Pincode' }
            ];

            for (let field of addressFields) {
                const fieldElement = $(`#${field.id}`);
                if (!fieldElement.val() || fieldElement.val().trim() === '') {
                    return {
                        valid: false,
                        step: 1,
                        field: fieldElement,
                        message: `Please fill ${field.name}.`
                    };
                }
                if (field.id === 'customerPincode' && fieldElement.val().length !== 6) {
                    return {
                        valid: false,
                        step: 1,
                        field: fieldElement,
                        message: 'Pincode must be 6 digits.'
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

        // OTP validation bypassed for now
        /*
        const mobileOTP = $('#mobileOTP');
        if (!mobileOTP.length || !mobileOTP.val() || mobileOTP.val().trim() === '') {
            return {
                valid: false,
                step: 2,
                field: mobileOTP,
                message: 'Please enter mobile OTP.'
            };
        }
        */

        return { valid: true };
    }

    function validateStep3() {
        // OTP validation bypassed for now
        /*
        const aadharOTP = $('#aadharOTP');
        if (!aadharOTP.length || !aadharOTP.val() || aadharOTP.val().trim() === '') {
            return {
                valid: false,
                step: 3,
                field: aadharOTP,
                message: 'Please enter Aadhar OTP.'
            };
        }
        */

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

    // Unified handler for existing user selection
    $('#existingUserId').on('change', function () {
        const userId = $(this).val();
        const userData = usersDataMap[userId];

        if (userData && userData.id) {
            // Pre-fill form fields
            $('#customerFirstName').val(userData.first_name || userData.name.split(' ')[0]);
            $('#customerLastName').val(userData.last_name || userData.name.split(' ').slice(1).join(' '));
            $('#customerAadhar').val(userData.aadhar_number);
            $('#customerMobile').val(userData.phone_number);
            $('#customerAltMobile').val(userData.alternative_phone_number);
            $('#customerEmail').val(userData.email);
            $('#customerPAN').val(userData.pan_number);

            if (userData.address_type) {
                $('#customerAddressType').val(userData.address_type).trigger('change');

                // Populate address details after small delay to allow container fade-in
                setTimeout(() => {
                    $('#customerFlatBuilding').val(userData.address);
                    $('#customerLocality').val(userData.area);
                    $('#customerPincode').val(userData.zip_code);

                    if (userData.state) {
                        $('#customerState').val(userData.state).trigger('change');

                        // Populate city after state change triggers city load
                        setTimeout(() => {
                            if (userData.city) {
                                $('#customerCity').val(userData.city);
                            }
                        }, 200);
                    }
                }, 300);
            }

            if (userData.employment_type) {
                $('#customerEmploymentType').val(userData.employment_type);
            }

            // OTP Logic
            window.selectedUserMobile = userData.phone_number;
            if (window.selectedUserMobile && window.selectedUserMobile.length === 10) {
                sendLoanOtp(window.selectedUserMobile);
            }

            // Show notification
            $('.user-selected-notification').remove();
            const notification = $(`<div class="alert alert-info alert-dismissible fade show user-selected-notification" role="alert" style="margin-top: 10px;">
                <strong><i class="bi bi-check-circle me-2"></i>User Selected:</strong> ${userData.name} (${userData.email})
                <div class="small mt-1">Fields have been pre-filled. You can edit them if needed.</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>`);
            $('#existingCustomerSection').after(notification);
            setTimeout(() => notification.fadeOut(() => notification.remove()), 8000);
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

