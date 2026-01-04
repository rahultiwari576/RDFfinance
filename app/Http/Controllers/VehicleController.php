<?php

namespace App\Http\Controllers;

use App\Models\VehicleCompany;
use App\Models\VehicleModel;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class VehicleController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }
    /**
     * Display vehicle management page
     */
    public function index()
    {
        $companies = VehicleCompany::with('activeModels')->orderBy('name')->get();
        return view('admin.vehicles.index', compact('companies'));
    }

    /**
     * Store a new vehicle company
     */
    public function storeCompany(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:vehicle_companies,name'],
        ]);

        $company = VehicleCompany::create([
            'name' => $validated['name'],
            'is_active' => true,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Vehicle company created successfully.',
            'company' => $company,
        ]);
    }

    /**
     * Update a vehicle company
     */
    public function updateCompany(Request $request, $id): JsonResponse
    {
        $company = VehicleCompany::findOrFail($id);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:vehicle_companies,name,' . $id],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $company->update([
            'name' => $validated['name'],
            'is_active' => $validated['is_active'] ?? $company->is_active,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Vehicle company updated successfully.',
            'company' => $company,
        ]);
    }

    /**
     * Delete a vehicle company
     */
    public function deleteCompany($id): JsonResponse
    {
        $company = VehicleCompany::findOrFail($id);
        $company->delete();

        return response()->json([
            'status' => true,
            'message' => 'Vehicle company deleted successfully.',
        ]);
    }

    /**
     * Store a new vehicle model
     */
    public function storeModel(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'vehicle_company_id' => ['required', 'exists:vehicle_companies,id'],
            'name' => ['required', 'string', 'max:255'],
        ]);

        // Check if model already exists for this company
        $existing = VehicleModel::where('vehicle_company_id', $validated['vehicle_company_id'])
            ->where('name', $validated['name'])
            ->first();

        if ($existing) {
            return response()->json([
                'status' => false,
                'message' => 'This model already exists for the selected company.',
            ], 422);
        }

        $model = VehicleModel::create([
            'vehicle_company_id' => $validated['vehicle_company_id'],
            'name' => $validated['name'],
            'is_active' => true,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Vehicle model created successfully.',
            'model' => $model,
        ]);
    }

    /**
     * Update a vehicle model
     */
    public function updateModel(Request $request, $id): JsonResponse
    {
        $model = VehicleModel::findOrFail($id);

        $validated = $request->validate([
            'vehicle_company_id' => ['required', 'exists:vehicle_companies,id'],
            'name' => ['required', 'string', 'max:255'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        // Check if model already exists for this company (excluding current model)
        $existing = VehicleModel::where('vehicle_company_id', $validated['vehicle_company_id'])
            ->where('name', $validated['name'])
            ->where('id', '!=', $id)
            ->first();

        if ($existing) {
            return response()->json([
                'status' => false,
                'message' => 'This model already exists for the selected company.',
            ], 422);
        }

        $model->update([
            'vehicle_company_id' => $validated['vehicle_company_id'],
            'name' => $validated['name'],
            'is_active' => $validated['is_active'] ?? $model->is_active,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Vehicle model updated successfully.',
            'model' => $model,
        ]);
    }

    /**
     * Delete a vehicle model
     */
    public function deleteModel($id): JsonResponse
    {
        $model = VehicleModel::findOrFail($id);
        $model->delete();

        return response()->json([
            'status' => true,
            'message' => 'Vehicle model deleted successfully.',
        ]);
    }

    /**
     * Get models by company ID (for cascading dropdown)
     */
    public function getModelsByCompany($companyId): JsonResponse
    {
        $models = VehicleModel::where('vehicle_company_id', $companyId)
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'vehicle_company_id']);

        return response()->json([
            'status' => true,
            'models' => $models,
        ]);
    }

    /**
     * Get all companies (for API)
     */
    public function getCompanies(): JsonResponse
    {
        $companies = VehicleCompany::where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name']);

        return response()->json([
            'status' => true,
            'companies' => $companies,
        ]);
    }
}

