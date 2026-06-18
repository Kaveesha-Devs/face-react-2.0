<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Employee\StoreEmployeeRequest;
use App\Http\Requests\Employee\UpdateEmployeeRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class EmployeeController extends Controller
{
    use \App\Traits\ApiResponse;

    /**
     * List all employees in admin's company
     * GET /api/employees
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $query = User::byCompany($user->company_id)
            ->employees()
            ->with(['department', 'section'])
            ->latest();

        // Optional filters
        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('username', 'like', "%{$search}%")
                    ->orWhere('employee_id', 'like', "%{$search}%");
            });
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        $employees = $query->paginate($request->query('per_page', 15));

        return $this->successResponse([
            'data' => UserResource::collection($employees),
            'meta' => [
                'total' => $employees->total(),
                'per_page' => $employees->perPage(),
                'current_page' => $employees->currentPage(),
                'last_page' => $employees->lastPage(),
            ],
        ]);
    }

    /**
     * Create new employee
     * POST /api/employees
     */
    public function store(StoreEmployeeRequest $request): JsonResponse
    {
        $admin = $request->user();

        $avatarPath = null;
        if ($request->hasFile('avatar')) {
            $avatarPath = $request->file('avatar')->store('employees/avatars', 'public');
        }

        $employee = User::create([
            'company_id' => $admin->company_id,
            'department_id' => $request->department_id,
            'section_id' => $request->section_id,
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'employee_id' => $request->employee_id,
            'avatar' => $avatarPath,
            'role' => User::ROLE_EMPLOYEE,
            'language' => $request->language ?? 'en',
        ]);

        return $this->successResponse(
            new UserResource($employee->load(['department', 'section'])),
            'Employee created successfully.',
            201
        );
    }

    /**
     * Get single employee
     * GET /api/employees/{employee}
     */
    public function show(Request $request, User $employee): JsonResponse
    {
        $this->authorize('view', $employee);

        return $this->successResponse(new UserResource($employee->load(['department', 'section', 'company'])));
    }

    /**
     * Update employee
     * PUT /api/employees/{employee}
     */
    public function update(UpdateEmployeeRequest $request, User $employee): JsonResponse
    {
        $this->authorize('update', $employee);

        if ($request->hasFile('avatar')) {
            if ($employee->avatar) {
                Storage::disk('public')->delete($employee->avatar);
            }
            $employee->avatar = $request->file('avatar')->store('employees/avatars', 'public');
        }

        $data = $request->validated();

        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        $employee->update($data);

        return $this->successResponse(
            new UserResource($employee->fresh()->load(['department', 'section'])),
            'Employee updated successfully.'
        );
    }

    /**
     * Activate / Deactivate employee
     * PATCH /api/employees/{employee}/toggle-status
     */
    public function toggleStatus(Request $request, User $employee): JsonResponse
    {
        $this->authorize('update', $employee);

        $employee->update(['is_active' => !$employee->is_active]);

        return $this->successResponse(['is_active' => $employee->is_active], 'Employee status updated.');
    }

    /**
     * Delete employee (soft delete)
     * DELETE /api/employees/{employee}
     */
    public function destroy(Request $request, User $employee): JsonResponse
    {
        $this->authorize('delete', $employee);

        $employee->delete();

        return $this->successResponse([], 'Employee removed successfully.');
    }
}