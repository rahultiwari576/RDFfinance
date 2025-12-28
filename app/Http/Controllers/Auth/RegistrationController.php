<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\AadharExtractRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use App\Services\AadharExtractorService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class RegistrationController extends Controller
{
    public function __construct(private readonly AadharExtractorService $aadharExtractorService)
    {
    }

    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    public function extractAadhar(AadharExtractRequest $request): JsonResponse
    {
        $file = $request->file('aadhar_document');

        $aadharNumber = $this->aadharExtractorService->extractFromUploadedFile($file);

        if (!$aadharNumber) {
            return response()->json([
                'status' => false,
                'message' => 'Unable to extract Aadhar number. Please ensure the document is clear.',
            ], 422);
        }

        $exists = User::where('aadhar_number', $aadharNumber)->exists();

        if ($exists) {
            return response()->json([
                'status' => false,
                'message' => 'Aadhar Number Already Registered',
            ], 409);
        }

        return response()->json([
            'status' => true,
            'aadhar_number' => $aadharNumber,
        ]);
    }

    public function register(RegisterRequest $request): JsonResponse
    {
        $validated = $request->validated();

        if (User::where('aadhar_number', $validated['aadhar_number'])->exists()) {
            return response()->json([
                'status' => false,
                'message' => 'Aadhar Number Already Registered',
            ], 409);
        }

        // $aadharPath = $request->file('aadhar_document')->store('documents/aadhar', 'public');
        // $panPath = $request->file('pan_document')->store('documents/pan', 'public');

        // Build full name from first, middle, and last name
        $fullName = trim($validated['first_name'] . ' ' . ($validated['middle_name'] ?? '') . ' ' . $validated['last_name']);
        $fullName = preg_replace('/\s+/', ' ', $fullName); // Remove extra spaces

        $userData = [
            'name' => $fullName,
            'first_name' => $validated['first_name'],
            'middle_name' => $validated['middle_name'] ?? null,
            'last_name' => $validated['last_name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'user', // Default role for new registrations
            'aadhar_number' => $validated['aadhar_number'],
            'alternative_phone_number' => $validated['alternative_phone_number'] ?? null,
            'address_type' => $validated['address_type'],
            'address' => $validated['address'] ?? null,
            'employment_type' => $validated['employment_type'],
            'pan_number' => $validated['pan_number'],
            'phone_number' => $validated['phone_number'],
            'age' => $validated['age'],
        ];

        // Handle driving license upload if provided
        if ($request->hasFile('driving_license')) {
            $drivingLicensePath = $request->file('driving_license')->store('documents/driving_license', 'public');
            $userData['driving_license_path'] = $drivingLicensePath;
        }

        $user = User::create($userData);

        // $user->forceFill([
        //     'aadhar_document_path' => $aadharPath,
        //     'pan_document_path' => $panPath,
        // ])->save();

        return response()->json([
            'status' => true,
            'message' => 'Registration successful. Please login to continue.',
            'redirect' => route('login'),
        ], 201);
    }
}

