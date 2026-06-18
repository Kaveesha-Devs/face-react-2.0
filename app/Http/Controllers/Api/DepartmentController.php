<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Department\StoreDepartmentRequest;
use App\Http\Requests\Department\UpdateDepartmentRequest;
use App\Http\Resources\DepartmentResource;
use App\Models\Department;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    use \App\Traits\ApiResponse;

    /**
     * List departments for the authenticated manager's company.
     * GET /api/departments
     */
    public function index(Request $request): JsonResponse
    {
        // Fetch departments tied to the user's company, including their sections
        $departments = Department::where('company_id', $request->user()->company_id)
            ->active() // Assumes scopeActive exists in Department model
            ->with('sections')
            ->withCount('users')
            ->get();

        return $this->successResponse(
            DepartmentResource::collection($departments),
            'Departments retrieved successfully.'
        );
    }

    /**
     * Create a new department.
     * POST /api/departments
     */
    public function store(StoreDepartmentRequest $request): JsonResponse
    {
        // Note: Authorization logic is handled in StoreDepartmentRequest@authorize
        
        $department = Department::create([
            'company_id'   => $request->user()->company_id, // Derived from token for security
            'name'         => $request->name,
            'is_active'    => true,
        ]);

        // Load sections (will be empty) to keep the resource structure consistent
        return $this->successResponse(
            new DepartmentResource($department->load('sections')),
            'Department created successfully.',
            201
        );
    }

    /**
     * Get a single department.
     * GET /api/departments/{department}
     */
    public function show(Request $request, Department $department): JsonResponse
    {
        $this->authorize('view', $department);

        return $this->successResponse(
            new DepartmentResource($department->load('sections')),
        );
    }

    /**
     * Update an existing department.
     * PUT /api/departments/{department}
     */
    public function update(UpdateDepartmentRequest $request, Department $department): JsonResponse
    {
        // Ensure user has permission to update this specific department
        $this->authorize('update', $department);

        $department->update($request->validated());

        return $this->successResponse(
            new DepartmentResource($department->fresh()->load('sections')),
            'Department updated.'
        );
    }

    /**
     * Delete a department.
     * DELETE /api/departments/{department}
     */
    public function destroy(Request $request, Department $department): JsonResponse
    {
        // Ensure user has permission to delete
        $this->authorize('delete', $department);

        $department->delete();

        return $this->successResponse([], 'Department deleted.');
    }
}