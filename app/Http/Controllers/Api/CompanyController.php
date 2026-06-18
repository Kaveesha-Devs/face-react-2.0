<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Company\RegisterCompanyRequest;
use App\Http\Requests\Company\UpdateCompanyRequest;
use App\Http\Resources\CompanyResource;
use App\Models\Company;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class CompanyController extends Controller
{
    use \App\Traits\ApiResponse;

    /**
     * Register a new company + admin user
     * POST /api/companies/register
     * Public route — no auth required
     */
    public function register(RegisterCompanyRequest $request): JsonResponse
    {
        DB::beginTransaction();

        try {
            // 1. Create company
            // companies table: id, name, email, phone, address, is_active, created_at, updated_at, deleted_at
            $company = Company::create([
                'name'    => $request->name,
                'email'   => $request->email   ?? null,
                'phone'   => $request->phone   ?? null,
                'address' => $request->address ?? null,
            ]);

            // 2. Create admin user
            User::create([
                'username'   => $request->admin_username,
                'name'       => $request->admin_name,
                'email'      => $request->email ?? null,
                'password'   => Hash::make($request->admin_password),
                'company_id' => $company->id,
                'role'       => User::ROLE_ADMIN,
                'is_active'  => true,
            ]);

            DB::commit();

            return $this->successResponse(
                new CompanyResource($company),
                'Company registered successfully.',
                201
            );

        } catch (\Throwable $e) {
            DB::rollBack();

            return $this->errorResponse(
                'Registration failed. Please try again.',
                500,
                config('app.debug') ? $e->getMessage() : null
            );
        }
    }

    /**
     * Get company profile (admin only)
     * GET /api/companies/{company}
     */
    public function show(Request $request, Company $company): JsonResponse
    {
        $this->authorize('view', $company);

        return $this->successResponse(
            new CompanyResource($company->load('departments'))
        );
    }

    /**
     * Update company (admin only)
     * PUT /api/companies/{company}
     */
    public function update(UpdateCompanyRequest $request, Company $company): JsonResponse
    {
        $this->authorize('update', $company);

        if ($request->hasFile('logo')) {
            if ($company->logo) {
                Storage::disk('public')->delete($company->logo);
            }
            $company->logo = $request->file('logo')->store('companies/logos', 'public');
        }

        $company->update($request->validated());

        return $this->successResponse(
            new CompanyResource($company->fresh()),
            'Company updated successfully.'
        );
    }
}