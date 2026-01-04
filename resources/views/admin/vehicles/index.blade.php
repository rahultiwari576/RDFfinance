@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h2 class="mb-0">
                    <i class="bi bi-car-front me-2"></i>Vehicle Management
                </h2>
                <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-2"></i>Back to Dashboard
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Vehicle Companies Section -->
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-building me-2"></i>Vehicle Companies</h5>
                    <button class="btn btn-light btn-sm" data-bs-toggle="modal" data-bs-target="#addCompanyModal">
                        <i class="bi bi-plus-circle me-1"></i>Add Company
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Models</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="companiesTableBody">
                                @foreach($companies as $company)
                                <tr data-company-id="{{ $company->id }}">
                                    <td><strong>{{ $company->name }}</strong></td>
                                    <td>
                                        <span class="badge bg-info">{{ $company->activeModels->count() }} models</span>
                                    </td>
                                    <td>
                                        @if($company->is_active)
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-secondary">Inactive</span>
                                        @endif
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-primary edit-company" 
                                                data-id="{{ $company->id }}" 
                                                data-name="{{ $company->name }}"
                                                data-active="{{ $company->is_active }}">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button class="btn btn-sm btn-danger delete-company" data-id="{{ $company->id }}">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                        <button class="btn btn-sm btn-info view-models" 
                                                data-company-id="{{ $company->id }}"
                                                data-company-name="{{ $company->name }}">
                                            <i class="bi bi-list-ul"></i> Models
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Vehicle Models Section -->
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-car-front me-2"></i>Vehicle Models</h5>
                    <button class="btn btn-light btn-sm" data-bs-toggle="modal" data-bs-target="#addModelModal">
                        <i class="bi bi-plus-circle me-1"></i>Add Model
                    </button>
                </div>
                <div class="card-body">
                    <div id="modelsSection">
                        <p class="text-muted text-center py-5">
                            <i class="bi bi-info-circle me-2"></i>Select a company to view its models or add a new model
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add/Edit Company Modal -->
<div class="modal fade" id="addCompanyModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="companyModalTitle">Add Vehicle Company</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="companyForm">
                <div class="modal-body">
                    <input type="hidden" id="companyId">
                    <div class="mb-3">
                        <label class="form-label">Company Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="companyName" required>
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="companyIsActive" checked>
                            <label class="form-check-label" for="companyIsActive">
                                Active
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Company</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add/Edit Model Modal -->
<div class="modal fade" id="addModelModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modelModalTitle">Add Vehicle Model</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="modelForm">
                <div class="modal-body">
                    <input type="hidden" id="modelId">
                    <div class="mb-3">
                        <label class="form-label">Company <span class="text-danger">*</span></label>
                        <select class="form-select" id="modelCompanyId" required>
                            <option value="">Select Company...</option>
                            @foreach($companies as $company)
                                <option value="{{ $company->id }}">{{ $company->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Model Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="modelName" required>
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="modelIsActive" checked>
                            <label class="form-check-label" for="modelIsActive">
                                Active
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Save Model</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(function() {
    // Company Form Handler
    $('#companyForm').on('submit', function(e) {
        e.preventDefault();
        const companyId = $('#companyId').val();
        const data = {
            name: $('#companyName').val(),
            is_active: $('#companyIsActive').is(':checked') ? 1 : 0
        };

        const url = companyId 
            ? `/admin/vehicles/companies/${companyId}`
            : '/admin/vehicles/companies';
        const method = companyId ? 'PUT' : 'POST';

        axios({
            method: method,
            url: url,
            data: data
        })
        .then(({ data }) => {
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: data.message,
                timer: 2000
            });
            $('#addCompanyModal').modal('hide');
            location.reload();
        })
        .catch((error) => {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: error.response?.data?.message || 'Failed to save company.'
            });
        });
    });

    // Edit Company
    $(document).on('click', '.edit-company', function() {
        const id = $(this).data('id');
        const name = $(this).data('name');
        const active = $(this).data('active');

        $('#companyId').val(id);
        $('#companyName').val(name);
        $('#companyIsActive').prop('checked', active);
        $('#companyModalTitle').text('Edit Vehicle Company');
        $('#addCompanyModal').modal('show');
    });

    // Delete Company
    $(document).on('click', '.delete-company', function() {
        const id = $(this).data('id');
        
        Swal.fire({
            title: 'Are you sure?',
            text: 'This will delete the company and all its models!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                axios.delete(`/admin/vehicles/companies/${id}`)
                .then(({ data }) => {
                    Swal.fire('Deleted!', data.message, 'success');
                    location.reload();
                })
                .catch((error) => {
                    Swal.fire('Error!', error.response?.data?.message || 'Failed to delete company.', 'error');
                });
            }
        });
    });

    // Model Form Handler
    $('#modelForm').on('submit', function(e) {
        e.preventDefault();
        const modelId = $('#modelId').val();
        const data = {
            vehicle_company_id: $('#modelCompanyId').val(),
            name: $('#modelName').val(),
            is_active: $('#modelIsActive').is(':checked') ? 1 : 0
        };

        const url = modelId 
            ? `/admin/vehicles/models/${modelId}`
            : '/admin/vehicles/models';
        const method = modelId ? 'PUT' : 'POST';

        axios({
            method: method,
            url: url,
            data: data
        })
        .then(({ data }) => {
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: data.message,
                timer: 2000
            });
            $('#addModelModal').modal('hide');
            location.reload();
        })
        .catch((error) => {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: error.response?.data?.message || 'Failed to save model.'
            });
        });
    });

    // View Models for Company
    $(document).on('click', '.view-models', function() {
        const companyId = $(this).data('company-id');
        const companyName = $(this).data('company-name');
        
        axios.get(`/admin/vehicles/companies/${companyId}/models`)
        .then(({ data }) => {
            let html = `<h6 class="mb-3">Models for <strong>${companyName}</strong></h6>`;
            html += '<div class="table-responsive"><table class="table table-sm">';
            html += '<thead><tr><th>Model Name</th><th>Status</th><th>Actions</th></tr></thead><tbody>';
            
            if (data.models.length === 0) {
                html += '<tr><td colspan="3" class="text-center text-muted">No models found</td></tr>';
            } else {
                data.models.forEach(model => {
                    html += `<tr data-model-id="${model.id}">
                        <td>${model.name}</td>
                        <td><span class="badge bg-success">Active</span></td>
                        <td>
                            <button class="btn btn-sm btn-primary edit-model" 
                                    data-id="${model.id}" 
                                    data-company-id="${model.vehicle_company_id}"
                                    data-name="${model.name}">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <button class="btn btn-sm btn-danger delete-model" data-id="${model.id}">
                                <i class="bi bi-trash"></i>
                            </button>
                        </td>
                    </tr>`;
                });
            }
            
            html += '</tbody></table></div>';
            $('#modelsSection').html(html);
        })
        .catch((error) => {
            Swal.fire('Error!', 'Failed to load models.', 'error');
        });
    });

    // Edit Model
    $(document).on('click', '.edit-model', function() {
        const id = $(this).data('id');
        const companyId = $(this).data('company-id');
        const name = $(this).data('name');

        $('#modelId').val(id);
        $('#modelCompanyId').val(companyId);
        $('#modelName').val(name);
        $('#modelModalTitle').text('Edit Vehicle Model');
        $('#addModelModal').modal('show');
    });

    // Delete Model
    $(document).on('click', '.delete-model', function() {
        const id = $(this).data('id');
        
        Swal.fire({
            title: 'Are you sure?',
            text: 'This will delete the model!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                axios.delete(`/admin/vehicles/models/${id}`)
                .then(({ data }) => {
                    Swal.fire('Deleted!', data.message, 'success');
                    location.reload();
                })
                .catch((error) => {
                    Swal.fire('Error!', error.response?.data?.message || 'Failed to delete model.', 'error');
                });
            }
        });
    });

    // Reset modals on close
    $('#addCompanyModal').on('hidden.bs.modal', function() {
        $('#companyForm')[0].reset();
        $('#companyId').val('');
        $('#companyModalTitle').text('Add Vehicle Company');
    });

    $('#addModelModal').on('hidden.bs.modal', function() {
        $('#modelForm')[0].reset();
        $('#modelId').val('');
        $('#modelModalTitle').text('Add Vehicle Model');
    });
});
</script>
@endpush
@endsection

