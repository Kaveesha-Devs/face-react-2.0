<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ReactLog;
use App\Models\ReactType;
use App\Models\Department;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    use \App\Traits\ApiResponse;

    /**
     * Main dashboard stats
     * GET /api/dashboard
     * Query params: date_from, date_to, department_id, section_id, hour_from, hour_to
     */
    public function index(Request $request): JsonResponse
    {
        $user         = $request->user();
        $companyId    = $user->company_id;
        $departmentId = $request->query('department_id');
        $sectionId    = $request->query('section_id');
        $dateFrom     = $request->query('date_from', today()->toDateString());
        $dateTo       = $request->query('date_to', today()->toDateString());
        $hourFrom     = $request->query('hour_from');
        $hourTo       = $request->query('hour_to');

        // Base query with all filters
        $baseQuery = ReactLog::byCompany($companyId)
            ->byDepartment($departmentId)
            ->bySection($sectionId)
            ->byDateRange($dateFrom, $dateTo)
            ->byHourRange($hourFrom, $hourTo);

        $totalReacts = (clone $baseQuery)->count();

        // Per react type counts + percentages
        $reactTypes = ReactType::active()->get();

        $breakdown = $reactTypes->map(function ($type) use ($baseQuery, $totalReacts) {
            $count = (clone $baseQuery)->where('react_type_id', $type->id)->count();

            return [
                'id'           => $type->id,
                'type'         => $type->type,
                'sinhala_type' => $type->sinhala_type,
                'tamil_type'   => $type->tamil_type,
                'icon_code'    => $type->icon_code,
                'count'        => $count,
                'percentage'   => $totalReacts > 0
                    ? round(($count / $totalReacts) * 100, 2)
                    : 0,
            ];
        });

        // Total employees in company (filtered by dept/section if given)
        $employeeQuery = User::active()
            ->byCompany($companyId);

        if ($departmentId) {
            $employeeQuery->where('department_id', $departmentId);
        }

        if ($sectionId) {
            $employeeQuery->where('section_id', $sectionId);
        }

        $totalEmployees   = $employeeQuery->count();
        $participationRate = $totalEmployees > 0
            ? round(($totalReacts / $totalEmployees) * 100, 2)
            : 0;

        return $this->successResponse([
            'total_reacts' => $totalReacts,
            'participation_rate' => $participationRate,
            'filters'           => [
                'date_from'     => $dateFrom,
                'date_to'       => $dateTo,
                'department_id' => $departmentId ? (int)$departmentId : null,
                'section_id'    => $sectionId    ? (int)$sectionId    : null,
                'hour_from'     => $hourFrom      ? (int)$hourFrom     : null,
                'hour_to'       => $hourTo        ? (int)$hourTo       : null,
            ],
            'breakdown'         => $breakdown,
        ]);
    }

    /**
     * Dashboard stats alias
     * GET /api/dashboard/stats
     */
    public function stats(Request $request): JsonResponse
    {
        return $this->index($request);
    }

    /**
     * Data formatted for Pie Chart
     * GET /api/dashboard/pie-chart
     * Query params: date_from, date_to, department_id, section_id, hour_from, hour_to
     */
    public function pieChart(Request $request): JsonResponse
    {
        $user         = $request->user();
        $companyId    = $user->company_id;
        $departmentId = $request->query('department_id');
        $sectionId    = $request->query('section_id');
        $dateFrom     = $request->query('date_from', today()->toDateString());
        $dateTo       = $request->query('date_to', today()->toDateString());
        $hourFrom     = $request->query('hour_from');
        $hourTo       = $request->query('hour_to');

        $data = ReactType::active()
            ->withCount([
                'reactLogs as count' => function ($q) use ($companyId, $departmentId, $sectionId, $dateFrom, $dateTo, $hourFrom, $hourTo) {
                    $q->byCompany($companyId)
                      ->byDepartment($departmentId)
                      ->bySection($sectionId)
                      ->byDateRange($dateFrom, $dateTo)
                      ->byHourRange($hourFrom, $hourTo);
                }
            ])
            ->get()
            ->map(fn($type) => [
                'label' => $type->type,
                'si'    => $type->sinhala_type,
                'ta'    => $type->tamil_type,
                'icon'  => $type->icon_code,
                'value' => $type->count,
                'color' => $this->getColorForType($type->id),
            ]);

        return $this->successResponse($data);
    }

    /**
     * Data formatted for Bar Chart (daily trend)
     * GET /api/dashboard/bar-chart
     * Query params: date_from, date_to, department_id, section_id, hour_from, hour_to
     */
    public function barChart(Request $request): JsonResponse
    {
        $user         = $request->user();
        $companyId    = $user->company_id;
        $departmentId = $request->query('department_id');
        $sectionId    = $request->query('section_id');
        $dateFrom     = $request->query('date_from', now()->subDays(6)->toDateString());
        $dateTo       = $request->query('date_to', today()->toDateString());
        $hourFrom     = $request->query('hour_from');
        $hourTo       = $request->query('hour_to');

        $reactTypes = ReactType::active()->get();

        // Get daily counts per react type
        $rawData = ReactLog::byCompany($companyId)
            ->byDepartment($departmentId)
            ->bySection($sectionId)
            ->byDateRange($dateFrom, $dateTo)
            ->byHourRange($hourFrom, $hourTo)
            ->select(
                DB::raw('DATE(created_at) as date'),
                'react_type_id',
                DB::raw('COUNT(*) as count')
            )
            ->groupBy('date', 'react_type_id')
            ->orderBy('date')
            ->get();

        // Build chart-friendly structure
        $dates    = collect();
        $datasets = $reactTypes->map(fn($type) => [
            'label'           => $type->type,
            'icon'            => $type->icon_code,
            'backgroundColor' => $this->getColorForType($type->id),
            'data'            => collect(),
        ])->keyBy(fn($_, $i) => $reactTypes[$i]->id);

        // Fill dates range
        $current = \Carbon\Carbon::parse($dateFrom);
        $end     = \Carbon\Carbon::parse($dateTo);

        while ($current <= $end) {
            $dateStr = $current->toDateString();
            $dates->push($dateStr);

            foreach ($reactTypes as $type) {
                $count = $rawData->where('date', $dateStr)
                    ->where('react_type_id', $type->id)
                    ->first()?->count ?? 0;

                $datasets[$type->id]['data']->push($count);
            }

            $current->addDay();
        }

        return $this->successResponse([
            'labels'   => $dates,
            'datasets' => $datasets->values(),
        ]);
    }

    /**
     * Department-wise summary
     * GET /api/dashboard/by-department
     * Query params: date_from, date_to, hour_from, hour_to
     */
    public function byDepartment(Request $request): JsonResponse
    {
        $user      = $request->user();
        $companyId = $user->company_id;
        $dateFrom  = $request->query('date_from', today()->toDateString());
        $dateTo    = $request->query('date_to', today()->toDateString());
        $hourFrom  = $request->query('hour_from');
        $hourTo    = $request->query('hour_to');

        $reactTypes = ReactType::active()->get();

        // ✅ Single query: get all dept+type counts at once (no N+1)
        $rawCounts = ReactLog::byCompany($companyId)
            ->byDateRange($dateFrom, $dateTo)
            ->byHourRange($hourFrom, $hourTo)
            ->whereNotNull('department_id')
            ->select('department_id', 'react_type_id', DB::raw('COUNT(*) as count'))
            ->groupBy('department_id', 'react_type_id')
            ->get()
            ->groupBy('department_id');

        $departments = Department::where('company_id', $companyId)
            ->active()
            ->get()
            ->map(function ($dept) use ($reactTypes, $rawCounts) {
                $deptCounts  = $rawCounts->get($dept->id, collect());
                $totalReacts = $deptCounts->sum('count');

                $breakdown = $reactTypes->map(function ($type) use ($deptCounts, $totalReacts) {
                    $count = $deptCounts->where('react_type_id', $type->id)->first()?->count ?? 0;
                    return [
                        'type'       => $type->type,
                        'icon'       => $type->icon_code,
                        'count'      => $count,
                        'percentage' => $totalReacts > 0
                            ? round(($count / $totalReacts) * 100, 2)
                            : 0,
                    ];
                });

                return [
                    'department_id'   => $dept->id,
                    'department_name' => $dept->name,
                    'total_reacts'    => $totalReacts,
                    'breakdown'       => $breakdown,
                ];
            });

        return $this->successResponse($departments);
    }

    /**
     * Section-wise summary (NEW)
     * GET /api/dashboard/by-section
     * Query params: date_from, date_to, department_id, hour_from, hour_to
     */
    public function bySection(Request $request): JsonResponse
    {
        $user         = $request->user();
        $companyId    = $user->company_id;
        $departmentId = $request->query('department_id');
        $dateFrom     = $request->query('date_from', today()->toDateString());
        $dateTo       = $request->query('date_to', today()->toDateString());
        $hourFrom     = $request->query('hour_from');
        $hourTo       = $request->query('hour_to');

        $sectionsQuery = \App\Models\Section::query()
            ->whereHas('department', fn($q) => $q->where('company_id', $companyId))
            ->active();

        if ($departmentId) {
            $sectionsQuery->where('department_id', $departmentId);
        }

        $sections = $sectionsQuery
            ->with('department:id,name')
            ->withCount([
                'users as total_employees' => fn($q) => $q->where('is_active', true)
            ])
            ->get()
            ->map(function ($section) use ($dateFrom, $dateTo, $hourFrom, $hourTo) {

                $totalReacts = ReactLog::where('section_id', $section->id)
                    ->byDateRange($dateFrom, $dateTo)
                    ->byHourRange($hourFrom, $hourTo)
                    ->count();

                $breakdown = ReactType::active()
                    ->withCount([
                        'reactLogs as count' => function ($q) use ($section, $dateFrom, $dateTo, $hourFrom, $hourTo) {
                            $q->where('section_id', $section->id)
                              ->byDateRange($dateFrom, $dateTo)
                              ->byHourRange($hourFrom, $hourTo);
                        }
                    ])
                    ->get()
                    ->map(fn($type) => [
                        'type'       => $type->type,
                        'icon'       => $type->icon_code,
                        'count'      => $type->count,
                        'percentage' => $totalReacts > 0
                            ? round(($type->count / $totalReacts) * 100, 2)
                            : 0,
                    ]);

                return [
                    'section_id'       => $section->id,
                    'section_name'     => $section->name,
                    'department_id'    => $section->department_id,
                    'department_name'  => $section->department->name ?? 'N/A',
                    'total_employees'  => $section->total_employees,
                    'total_reacts'     => $totalReacts,
                    'breakdown'        => $breakdown,
                ];
            });

        return $this->successResponse($sections);
    }

    /**
     * Assign colors to react types
     */
    private function getColorForType(int $typeId): string
    {
        return match ($typeId) {
            1 => '#22c55e',  // Excellent - green
            2 => '#3b82f6',  // Good      - blue
            3 => '#eab308',  // Average   - yellow
            4 => '#f97316',  // Poor      - orange
            5 => '#ef4444',  // Very Poor - red
            default => '#6b7280',
        };
    }
}