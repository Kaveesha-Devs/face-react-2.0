<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Section;

class SectionController extends Controller
{
    use \App\Traits\ApiResponse;

    public function store(Request $request): JsonResponse
    {
        // Only admins can create sections
        if (!$request->user()->hasAdminAccess()) {
            return $this->errorResponse('Access denied. Admin role required.', 403);
        }

        $request->validate([
            'name'          => 'required|string|max:100',
            'department_id' => 'required|exists:departments,id',
        ]);

        $section = Section::create([
            'department_id' => $request->department_id,
            'name'          => $request->name,
            'is_active'     => true,
        ]);

        return $this->successResponse($section, 'Section created successfully.', 201);
    }
}