<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ReactLogResource;
use App\Models\ReactLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ReactLogController extends Controller
{
    use \App\Traits\ApiResponse;

    /**
     * Store a new reaction log.
     * POST /api/reactions
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'reaction_id'   => 'required|integer|exists:react_types,id',
            'department_id' => 'nullable|integer|exists:departments,id',
            'section_id'    => 'nullable|integer|exists:sections,id',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors()->first(), 422);
        }

        $log = ReactLog::create([
            'user_id'       => $request->user()->id,
            'company_id'    => $request->user()->company_id,
            'react_type_id' => $request->input('reaction_id'),
            'department_id' => $request->input('department_id'),
            'section_id'    => $request->input('section_id'),
            // created_at and updated_at handled automatically by Laravel
        ]);

        return $this->successResponse(
            new ReactLogResource($log),
            'Feedback saved successfully.',
            201
        );
    }

    /**
     * Get filtered react logs for the app results screen.
     * GET /api/logs
     */
    public function index(Request $request): JsonResponse
    {
        $user      = $request->user();
        $companyId = $user->company_id;

        $dateFrom     = $request->query('date_from', today()->toDateString());
        $dateTo       = $request->query('date_to', today()->toDateString());
        $departmentId = $request->query('department_id');
        $sectionId    = $request->query('section_id');
        $hourFrom     = $request->query('hour_from');
        $hourTo       = $request->query('hour_to');
        $reactTypeId  = $request->query('react_type_id');
        $perPage      = min((int) $request->query('per_page', 20), 100);

        $query = ReactLog::query()
            ->with(['user', 'reactType', 'department', 'section'])
            ->byCompany($companyId)
            ->byDepartment($departmentId)
            ->bySection($sectionId)
            ->byDateRange($dateFrom, $dateTo)
            ->byHourRange($hourFrom, $hourTo)
            ->when($reactTypeId, fn($q) => $q->where('react_type_id', $reactTypeId))
            ->latest();

        $logs = $query->paginate($perPage);

        return $this->successResponse([
            'filters' => [
                'date_from'     => $dateFrom,
                'date_to'       => $dateTo,
                'department_id' => $departmentId ? (int) $departmentId : null,
                'section_id'    => $sectionId    ? (int) $sectionId    : null,
                'hour_from'     => $hourFrom      !== null ? (int) $hourFrom : null,
                'hour_to'       => $hourTo        !== null ? (int) $hourTo   : null,
                'react_type_id' => $reactTypeId   ? (int) $reactTypeId : null,
            ],
            'pagination' => [
                'total'        => $logs->total(),
                'per_page'     => $logs->perPage(),
                'current_page' => $logs->currentPage(),
                'last_page'    => $logs->lastPage(),
            ],
            'data' => ReactLogResource::collection($logs->items()),
        ]);
    }
}