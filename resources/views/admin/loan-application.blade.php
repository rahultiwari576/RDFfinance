<div class="modal fade" id="adminLoanApplicationModal" tabindex="-1" aria-labelledby="adminLoanApplicationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="adminLoanApplicationModalLabel">
                    <i class="bi bi-file-earmark-text me-2"></i>Loan Application Form
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="adminLoanApplicationForm" action="{{ route('admin.loans.apply') }}" enctype="multipart/form-data">
                    @csrf
                    
                    <!-- Progress Steps -->
                    <div class="progress-steps mb-4">
                        <div class="step active" data-step="1">
                            <div class="step-number">1</div>
                            <div class="step-label">Customer Info</div>
                        </div>
                        <div class="step-line"></div>
                        <div class="step" data-step="2">
                            <div class="step-number">2</div>
                            <div class="step-label">Vehicle & References</div>
                        </div>
                        <div class="step-line"></div>
                        <div class="step" data-step="3">
                            <div class="step-number">3</div>
                            <div class="step-label">Sanction & Bank</div>
                        </div>
                        <div class="step-line"></div>
                        <div class="step" data-step="4">
                            <div class="step-number">4</div>
                            <div class="step-label">Documents</div>
                        </div>
                    </div>

                    <!-- Customer Type Selection -->
                    <div class="mb-4">
                        <label class="form-label fw-bold">Customer Type <span class="text-danger">*</span></label>
                        <div class="btn-group w-100" role="group">
                            <input type="radio" class="btn-check" name="customer_type" id="newCustomer" value="new" checked>
                            <label class="btn btn-outline-primary" for="newCustomer">New Customer</label>
                            
                            <input type="radio" class="btn-check" name="customer_type" id="existingCustomer" value="existing">
                            <label class="btn btn-outline-primary" for="existingCustomer">Existing Customer</label>
                        </div>
                    </div>

                    <!-- Existing Customer Selection -->
                    <div class="mb-4 d-none" id="existingCustomerSection">
                        <label class="form-label fw-bold">Select Customer <span class="text-danger">*</span></label>
                        <select name="existing_user_id" id="existingUserId" class="form-control">
                            <option value="">Select Customer...</option>
                            <!-- Will be populated via AJAX -->
                        </select>
                    </div>

                    <!-- Page 1: Customer Information -->
                    <div class="form-step active" id="step1">
                        <h5 class="step-title mb-4">
                            <i class="bi bi-person me-2"></i>Page 1: Customer Information
                        </h5>
                        
                        <div class="row g-3" id="customerFieldsRow">
                            <div class="col-md-4">
                                <label class="form-label">First Name <span class="text-danger">*</span></label>
                                <input type="text" name="customer_first_name" id="customerFirstName" class="form-control" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Middle Name</label>
                                <input type="text" name="customer_middle_name" id="customerMiddleName" class="form-control">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Last Name <span class="text-danger">*</span></label>
                                <input type="text" name="customer_last_name" id="customerLastName" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Mother's Name <span class="text-danger">*</span></label>
                                <input type="text" name="customer_mother_name" id="customerMotherName" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Father's Name <span class="text-danger">*</span></label>
                                <input type="text" name="customer_father_name" id="customerFatherName" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Gender <span class="text-danger">*</span></label>
                                <select name="customer_gender" id="customerGender" class="form-control" required>
                                    <option value="male">Male</option>
                                    <option value="female">Female</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Date of Birth <span class="text-danger">*</span></label>
                                <input type="date" name="customer_dob" id="customerDOB" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Password <span class="text-danger">*</span></label>
                                <input type="password" name="customer_password" id="customerPassword" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Aadhar Number <span class="text-danger">*</span></label>
                                <input type="text" name="customer_aadhar_number" id="customerAadhar" class="form-control" maxlength="12" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Mobile Number <span class="text-danger">*</span></label>
                                <input type="text" name="customer_mobile_number" id="customerMobile" class="form-control" maxlength="10" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Alternative Mobile Number</label>
                                <input type="text" name="customer_alternative_mobile" id="customerAltMobile" class="form-control" maxlength="10">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" name="customer_email" id="customerEmail" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">PAN Number <span class="text-danger">*</span></label>
                                <input type="text" name="customer_pan_number" id="customerPAN" class="form-control" maxlength="10" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Address Type <span class="text-danger">*</span></label>
                                <select name="customer_address_type" id="customerAddressType" class="form-control" required>
                                    <option value="">Select...</option>
                                    <option value="RESIDENTIAL">RESIDENTIAL</option>
                                    <option value="PERMANENT">PERMANENT</option>
                                    <option value="OFFICE">OFFICE</option>
                                </select>
                            </div>

                            <!-- Dynamic Address Fields -->
                            <div id="addressDetailsContainer" class="col-12 d-none">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Flat / Building <span class="text-danger">*</span></label>
                                        <input type="text" name="customer_flat_building" id="customerFlatBuilding" class="form-control">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Locality <span class="text-danger">*</span></label>
                                        <input type="text" name="customer_locality" id="customerLocality" class="form-control">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">State <span class="text-danger">*</span></label>
                                        <select name="customer_state" id="customerState" class="form-select">
                                            <option value="">Select State...</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">City <span class="text-danger">*</span></label>
                                        <select name="customer_city" id="customerCity" class="form-select" disabled>
                                            <option value="">Select City...</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Pincode <span class="text-danger">*</span></label>
                                        <input type="text" name="customer_pincode" id="customerPincode" class="form-control" maxlength="6">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Employment Type <span class="text-danger">*</span></label>
                                <select name="customer_employment_type" id="customerEmploymentType" class="form-control" required>
                                    <option value="">Select...</option>
                                    <option value="self_employed">Self Employed</option>
                                    <option value="salaried">Salaried</option>
                                </select>
                            </div>
                        </div>

                        <!-- Guarantor Section -->
                        <hr class="my-4" id="guarantorSectionDivider">
                        <div id="guarantorSection">
                            <h6 class="mb-3"><i class="bi bi-person-badge me-2"></i>Guarantor / Co-Applicant Details</h6>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">First Name <span class="text-danger">*</span></label>
                                    <input type="text" name="guarantor_first_name" id="guarantorFirstName" class="form-control" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Last Name <span class="text-danger">*</span></label>
                                    <input type="text" name="guarantor_last_name" id="guarantorLastName" class="form-control" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Aadhar Number <span class="text-danger">*</span></label>
                                    <input type="text" name="guarantor_aadhar" id="guarantorAadhar" class="form-control" maxlength="12" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">PAN Number <span class="text-danger">*</span></label>
                                    <input type="text" name="guarantor_pan" id="guarantorPAN" class="form-control" maxlength="10" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Mobile Number <span class="text-danger">*</span></label>
                                    <input type="text" name="guarantor_mobile" id="guarantorMobile" class="form-control" maxlength="10" required>
                                </div>
                            </div>
                        </div>

                        <div class="form-actions mt-4 d-flex justify-content-between">
                            <button type="button" class="btn btn-outline-secondary" id="saveDraftStep1">
                                <i class="bi bi-save me-2"></i>Save Draft
                            </button>
                            <button type="button" class="btn btn-primary" id="nextToStep2">Next <i class="bi bi-arrow-right ms-2"></i></button>
                        </div>
                    </div>

                    <!-- Page 2: Vehicle & References -->
                    <div class="form-step d-none" id="step2">
                        <h5 class="step-title mb-4">
                            <i class="bi bi-car-front me-2"></i>Page 2: Vehicle Details & References
                        </h5>

                        <!-- Vehicle Type -->
                        <div class="mb-4">
                            <label class="form-label fw-bold">Vehicle Type <span class="text-danger">*</span></label>
                            <div class="btn-group w-100" role="group">
                                <input type="radio" class="btn-check" name="vehicle_type" id="newVehicle" value="new" checked>
                                <label class="btn btn-outline-primary" for="newVehicle">New Vehicle</label>
                                
                                <input type="radio" class="btn-check" name="vehicle_type" id="usedVehicle" value="used">
                                <label class="btn btn-outline-primary" for="usedVehicle">Used Vehicle</label>
                            </div>
                        </div>

                        <!-- New Vehicle Fields -->
                        <div id="newVehicleFields">
                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Company Name <span class="text-danger">*</span></label>
                                    <select name="vehicle_company_name" id="vehicleCompanyName" class="form-select" required>
                                        <option value="">Select Company...</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Model Name <span class="text-danger">*</span></label>
                                    <select name="vehicle_model_name" id="vehicleModelName" class="form-select" required disabled>
                                        <option value="">Select Company First...</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Dealer Details -->
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Dealer Name <span class="text-danger">*</span></label>
                                <input type="text" name="dealer_name" id="dealerName" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Dealer Mobile <span class="text-danger">*</span></label>
                                <input type="text" name="dealer_mobile" id="dealerMobile" class="form-control" maxlength="10" required>
                            </div>
                        </div>

                        <!-- Used Vehicle Fields -->
                        <div id="usedVehicleFields" class="d-none">
                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Company Name <span class="text-danger">*</span></label>
                                    <select name="used_vehicle_company" id="usedVehicleCompany" class="form-select">
                                        <option value="">Select Company...</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Model Name <span class="text-danger">*</span></label>
                                    <select name="used_vehicle_model" id="usedVehicleModel" class="form-select" disabled>
                                        <option value="">Select Company First...</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Engine Number <span class="text-danger">*</span></label>
                                    <input type="text" name="engine_number" id="engineNumber" class="form-control">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Chassis Number <span class="text-danger">*</span></label>
                                    <input type="text" name="chassis_number" id="chassisNumber" class="form-control">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Registration Number <span class="text-danger">*</span></label>
                                    <input type="text" name="registration_number" id="registrationNumber" class="form-control">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Registration Date <span class="text-danger">*</span></label>
                                    <input type="date" name="registration_date" id="registrationDate" class="form-control">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Registration Validity <span class="text-danger">*</span></label>
                                    <input type="date" name="registration_validity" id="registrationValidity" class="form-control">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Owner Name <span class="text-danger">*</span></label>
                                    <input type="text" name="owner_name" id="ownerName" class="form-control">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Vehicle Color <span class="text-danger">*</span></label>
                                    <input type="text" name="vehicle_color" id="vehicleColor" class="form-control">
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label">Other RC Details</label>
                                    <textarea name="rc_other_details" id="rcOtherDetails" class="form-control" rows="3"></textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Mobile OTP -->
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Mobile OTP <span class="text-danger">*</span></label>
                                <input type="text" name="mobile_otp" id="mobileOTP" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Office Address</label>
                                <textarea name="office_address" id="officeAddress" class="form-control" rows="2"></textarea>
                            </div>
                        </div>

                        <!-- References -->
                        <hr class="my-4">
                        <h6 class="mb-3"><i class="bi bi-people me-2"></i>References (Optional)</h6>
                        <div class="row g-3 mb-3">
                            <div class="col-md-4">
                                <label class="form-label">Reference 1 - Name</label>
                                <input type="text" name="reference1_name" id="reference1Name" class="form-control">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Reference 1 - Mobile</label>
                                <input type="text" name="reference1_mobile" id="reference1Mobile" class="form-control" maxlength="10">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Reference 1 - Address</label>
                                <textarea name="reference1_address" id="reference1Address" class="form-control" rows="2"></textarea>
                            </div>
                        </div>
                        <div class="row g-3 mb-3">
                            <div class="col-md-4">
                                <label class="form-label">Reference 2 - Name</label>
                                <input type="text" name="reference2_name" id="reference2Name" class="form-control">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Reference 2 - Mobile</label>
                                <input type="text" name="reference2_mobile" id="reference2Mobile" class="form-control" maxlength="10">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Reference 2 - Address</label>
                                <textarea name="reference2_address" id="reference2Address" class="form-control" rows="2"></textarea>
                            </div>
                        </div>

                        <!-- CIBIL Check -->
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label">CIBIL Check (By PAN) <span class="text-muted">(Optional)</span></label>
                                <button type="button" class="btn btn-info" id="checkCIBIL">
                                    <i class="bi bi-search me-2"></i>Check CIBIL Score
                                </button>
                                <input type="hidden" name="cibil_score" id="cibilScore">
                                <input type="hidden" name="cibil_details" id="cibilDetails">
                                <div id="cibilResult" class="mt-2"></div>
                            </div>
                        </div>

                        <div class="form-actions mt-4 d-flex justify-content-between">
                            <div>
                                <button type="button" class="btn btn-outline-secondary" id="prevToStep1">
                                    <i class="bi bi-arrow-left me-2"></i>Previous
                                </button>
                                <button type="button" class="btn btn-outline-info ms-2" id="saveDraftStep2">
                                    <i class="bi bi-save me-2"></i>Save Draft
                                </button>
                            </div>
                            <button type="button" class="btn btn-primary" id="nextToStep3">Next <i class="bi bi-arrow-right ms-2"></i></button>
                        </div>
                    </div>

                    <!-- Page 3: Sanction & Bank -->
                    <div class="form-step d-none" id="step3">
                        <h5 class="step-title mb-4">
                            <i class="bi bi-file-check me-2"></i>Page 3: Sanction Letter & Bank Details
                        </h5>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Aadhar OTP <span class="text-danger">*</span></label>
                                <input type="text" name="aadhar_otp" id="aadharOTP" class="form-control" required>
                            </div>
                        </div>

                        <!-- Sanction Letter -->
                        <div class="card mb-4">
                            <div class="card-header bg-info text-white">
                                <h6 class="mb-0"><i class="bi bi-file-text me-2"></i>Sanction Letter Details</h6>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label class="form-label">Loan Amount <span class="text-danger">*</span></label>
                                        <input type="number" name="principal_amount" id="loanAmount" class="form-control" min="1000" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Tenure (Months) <span class="text-danger">*</span></label>
                                        <input type="number" name="tenure_months" id="loanTenure" class="form-control" min="1" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">EMI Number <span class="text-danger">*</span></label>
                                        <input type="number" name="emi_count" id="emiCount" class="form-control" min="1" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Interest Rate (%) <span class="text-danger">*</span></label>
                                        <input type="number" name="interest_rate" id="interestRate" class="form-control" step="0.1" min="1" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Time Period</label>
                                        <input type="text" name="time_period" id="timePeriod" class="form-control">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Penalty Amount</label>
                                        <input type="number" name="penalty_amount" id="penaltyAmount" class="form-control" value="590" readonly>
                                        <small class="text-muted">Fixed: ₹590 (Max 3 times: 10th, 12th, 15th)</small>
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <label class="form-label">Sanction Letter Text</label>
                                    <textarea name="sanction_letter" id="sanctionLetter" class="form-control" rows="4" placeholder="Enter sanction letter details..."></textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Bank Details -->
                        <div class="card">
                            <div class="card-header bg-success text-white">
                                <h6 class="mb-0"><i class="bi bi-bank me-2"></i>Bank Details</h6>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Account Number <span class="text-danger">*</span></label>
                                        <input type="text" name="bank_account_number" id="bankAccountNumber" class="form-control" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Account Holder Name <span class="text-danger">*</span></label>
                                        <input type="text" name="bank_account_name" id="bankAccountName" class="form-control" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">IFSC Code <span class="text-danger">*</span></label>
                                        <input type="text" name="bank_ifsc" id="bankIFSC" class="form-control" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">UPI ID</label>
                                        <input type="text" name="bank_upi" id="bankUPI" class="form-control">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-actions mt-4 d-flex justify-content-between">
                            <div>
                                <button type="button" class="btn btn-outline-secondary" id="prevToStep2">
                                    <i class="bi bi-arrow-left me-2"></i>Previous
                                </button>
                                <button type="button" class="btn btn-outline-info ms-2" id="saveDraftStep3">
                                    <i class="bi bi-save me-2"></i>Save Draft
                                </button>
                            </div>
                            <button type="button" class="btn btn-primary" id="nextToStep4">Next <i class="bi bi-arrow-right ms-2"></i></button>
                        </div>
                    </div>

                    <!-- Page 4: Documents -->
                    <div class="form-step d-none" id="step4">
                        <h5 class="step-title mb-4">
                            <i class="bi bi-cloud-upload me-2"></i>Page 4: Document Uploads
                        </h5>

                        <!-- Dealer Documents -->
                        <div class="card mb-4">
                            <div class="card-header bg-primary text-white">
                                <h6 class="mb-0"><i class="bi bi-folder me-2"></i>Dealer Documents</h6>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Tax Invoice <span class="text-danger">*</span></label>
                                        <input type="file" name="tax_invoice" id="taxInvoice" class="form-control" accept=".pdf,.jpg,.jpeg,.png" required>
                                    </div>
                                    <div class="col-md-6" id="rcDocumentField">
                                        <label class="form-label">RC Document <span class="text-danger">*</span></label>
                                        <input type="file" name="rc_document" id="rcDocument" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Insurance <span class="text-danger">*</span></label>
                                        <input type="file" name="insurance" id="insurance" class="form-control" accept=".pdf,.jpg,.jpeg,.png" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Delivery Photo <span class="text-danger">*</span></label>
                                        <input type="file" name="delivery_photo" id="deliveryPhoto" class="form-control" accept=".jpg,.jpeg,.png" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Aadhar Card <span class="text-danger">*</span></label>
                                        <input type="file" name="aadhar_card" id="aadharCard" class="form-control" accept=".pdf,.jpg,.jpeg,.png" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">PAN Card <span class="text-danger">*</span></label>
                                        <input type="file" name="pan_card" id="panCard" class="form-control" accept=".pdf,.jpg,.jpeg,.png" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Address Proof (e.g., Electricity Bill) <span class="text-danger">*</span></label>
                                        <input type="file" name="address_proof" id="addressProof" class="form-control" accept=".pdf,.jpg,.jpeg,.png" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">RTO Booklet <span class="text-danger">*</span></label>
                                        <input type="file" name="rto_booklet" id="rtoBooklet" class="form-control" accept=".pdf,.jpg,.jpeg,.png" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Cheque 1 <span class="text-danger">*</span></label>
                                        <input type="file" name="cheque_1" id="cheque1" class="form-control" accept=".pdf,.jpg,.jpeg,.png" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Cheque 2 <span class="text-danger">*</span></label>
                                        <input type="file" name="cheque_2" id="cheque2" class="form-control" accept=".pdf,.jpg,.jpeg,.png" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Cheque 3 <span class="text-danger">*</span></label>
                                        <input type="file" name="cheque_3" id="cheque3" class="form-control" accept=".pdf,.jpg,.jpeg,.png" required>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Guarantor Documents -->
                        <div class="card">
                            <div class="card-header bg-warning text-dark">
                                <h6 class="mb-0"><i class="bi bi-person-badge me-2"></i>Guarantor Documents</h6>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label class="form-label">Guarantor Aadhar <span class="text-danger">*</span></label>
                                        <input type="file" name="guarantor_aadhar_doc" id="guarantorAadharDoc" class="form-control" accept=".pdf,.jpg,.jpeg,.png" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Guarantor PAN <span class="text-danger">*</span></label>
                                        <input type="file" name="guarantor_pan_doc" id="guarantorPanDoc" class="form-control" accept=".pdf,.jpg,.jpeg,.png" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Guarantor Cheque <span class="text-danger">*</span></label>
                                        <input type="file" name="guarantor_cheque" id="guarantorCheque" class="form-control" accept=".pdf,.jpg,.jpeg,.png" required>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-actions mt-4 d-flex justify-content-between">
                            <div>
                                <button type="button" class="btn btn-outline-secondary" id="prevToStep3">
                                    <i class="bi bi-arrow-left me-2"></i>Previous
                                </button>
                                <button type="button" class="btn btn-outline-info ms-2" id="saveDraftStep4">
                                    <i class="bi bi-save me-2"></i>Save Draft
                                </button>
                            </div>
                            <button type="submit" class="btn btn-success btn-lg" id="submitLoanApplication">
                                <i class="bi bi-check-circle me-2"></i>Submit Application
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
.progress-steps {
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 2rem;
}
.step {
    display: flex;
    flex-direction: column;
    align-items: center;
}
.step-number {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: #e2e8f0;
    color: #718096;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    transition: all 0.3s ease;
}
.step.active .step-number {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
}
.step-label {
    margin-top: 0.5rem;
    font-size: 0.75rem;
    color: #718096;
}
.step.active .step-label {
    color: #667eea;
    font-weight: 600;
}
.step-line {
    width: 100px;
    height: 2px;
    background: #e2e8f0;
    margin: 0 1rem;
    margin-top: -20px;
}
.form-step {
    animation: fadeIn 0.3s ease;
}
@keyframes fadeIn {
    from { opacity: 0; transform: translateX(20px); }
    to { opacity: 1; transform: translateX(0); }
}
/* Invalid field highlighting */
.form-control.is-invalid,
.form-select.is-invalid,
input.is-invalid {
    border-color: #dc3545 !important;
    box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25) !important;
    animation: shake 0.5s;
}
@keyframes shake {
    0%, 100% { transform: translateX(0); }
    10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
    20%, 40%, 60%, 80% { transform: translateX(5px); }
}
.step-title {
    color: #2d3748;
    font-weight: 600;
    border-bottom: 2px solid #e2e8f0;
    padding-bottom: 0.75rem;
}
</style>

@push('scripts')
<script src="{{ asset('js/admin-loan-application.js') }}"></script>
@endpush

