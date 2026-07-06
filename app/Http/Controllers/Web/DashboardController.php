<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Option;
use App\Models\OptionSubmission;
use App\Models\ReactLog;
use App\Models\ReactType;
use App\Models\Section;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Exports\ReactLogsExport;
use Maatwebsite\Excel\Facades\Excel;

class DashboardController extends Controller
{
    private function getCompanyId(): int
    {
        return Auth::user()->company_id;
    }

    // ── Main Dashboard View ──────────────────────────────────────────────
    public function index()
    {
        $user        = Auth::user();
        $companyId   = $user->company_id;
        $departments = Department::where('company_id', $companyId)->active()->get();
        $reactTypes  = ReactType::active()->get();

        return view('dashboard.index', compact('user', 'departments', 'reactTypes'));
    }

    // ── AJAX: Stats ──────────────────────────────────────────────────────
    public function stats(Request $request)
    {
        $companyId    = $this->getCompanyId();
        $departmentId = $request->query('department_id');
        $sectionId    = $request->query('section_id');
        $dateFrom     = $request->query('date_from', today()->toDateString());
        $dateTo       = $request->query('date_to', today()->toDateString());
        $hourFrom     = $request->query('hour_from');
        $hourTo       = $request->query('hour_to');

        $base = ReactLog::byCompany($companyId)
            ->byDepartment($departmentId)
            ->bySection($sectionId)
            ->byDateRange($dateFrom, $dateTo)
            ->byHourRange($hourFrom, $hourTo);

        $totalReacts = (clone $base)->count();
        $reactTypes  = ReactType::active()->get();

        $breakdown = $reactTypes->map(function ($type) use ($base, $totalReacts) {
            $count = (clone $base)->where('react_type_id', $type->id)->count();
            return [
                'id'         => $type->id,
                'type'       => $type->type,
                'icon'       => $type->icon_code,
                'color'      => $this->colorFor($type->id),
                'count'      => $count,
                'percentage' => $totalReacts > 0 ? round(($count / $totalReacts) * 100, 1) : 0,
            ];
        });

        // Top reaction
        $topReaction = $breakdown->sortByDesc('count')->first();

        // Satisfied = Excellent + Good
        $satisfied = $breakdown->whereIn('id', [1, 2])->sum('count');
        $satisfiedPct = $totalReacts > 0 ? round(($satisfied / $totalReacts) * 100, 1) : 0;

        // Employee count
        $empQuery = User::active()->byCompany($companyId);
        if ($departmentId) $empQuery->where('department_id', $departmentId);
        if ($sectionId)    $empQuery->where('section_id', $sectionId);
        $totalEmployees   = $empQuery->count();
        
        // Count accurately distinct employees who reacted
        $uniqueReactors   = (clone $base)->distinct('user_id')->count('user_id');
        $participationPct = $totalEmployees > 0 ? round(($uniqueReactors / $totalEmployees) * 100, 1) : 0;

        return response()->json([
            'total_reacts'       => $totalReacts,
            'total_employees'    => $totalEmployees,
            'satisfied_pct'      => $satisfiedPct,
            'participation_pct'  => $participationPct,
            'top_reaction'       => $topReaction,
            'breakdown'          => $breakdown->values(),
        ]);
    }

    // ── AJAX: Pie Chart ──────────────────────────────────────────────────
    public function pieChart(Request $request)
    {
        $companyId    = $this->getCompanyId();
        $departmentId = $request->query('department_id');
        $sectionId    = $request->query('section_id');
        $dateFrom     = $request->query('date_from', today()->toDateString());
        $dateTo       = $request->query('date_to', today()->toDateString());
        $hourFrom     = $request->query('hour_from');
        $hourTo       = $request->query('hour_to');

        $data = ReactType::active()
            ->withCount([
                'reactLogs as count' => fn($q) => $q
                    ->byCompany($companyId)->byDepartment($departmentId)
                    ->bySection($sectionId)->byDateRange($dateFrom, $dateTo)
                    ->byHourRange($hourFrom, $hourTo)
            ])
            ->get()
            ->map(fn($t) => [
                'label' => $t->type,
                'icon'  => $t->icon_code,
                'value' => $t->count,
                'color' => $this->colorFor($t->id),
            ]);

        return response()->json($data);
    }

    // ── AJAX: Bar Chart ──────────────────────────────────────────────────
    public function barChart(Request $request)
    {
        $companyId    = $this->getCompanyId();
        $departmentId = $request->query('department_id');
        $sectionId    = $request->query('section_id');
        $dateFrom     = $request->query('date_from', now()->subDays(6)->toDateString());
        $dateTo       = $request->query('date_to', today()->toDateString());
        $hourFrom     = $request->query('hour_from');
        $hourTo       = $request->query('hour_to');

        $reactTypes = ReactType::active()->get();

        $rawData = ReactLog::byCompany($companyId)
            ->byDepartment($departmentId)->bySection($sectionId)
            ->byDateRange($dateFrom, $dateTo)->byHourRange($hourFrom, $hourTo)
            ->select(DB::raw('DATE(created_at) as date'), 'react_type_id', DB::raw('COUNT(*) as count'))
            ->groupBy('date', 'react_type_id')
            ->orderBy('date')
            ->get();

        $dates    = collect();
        $datasets = $reactTypes->mapWithKeys(fn($t) => [
            $t->id => ['label' => $t->type, 'icon' => $t->icon_code, 'backgroundColor' => $this->colorFor($t->id), 'data' => collect()]
        ]);

        $current = \Carbon\Carbon::parse($dateFrom);
        $end     = \Carbon\Carbon::parse($dateTo);
        while ($current <= $end) {
            $ds = $current->toDateString();
            $dates->push($ds);
            foreach ($reactTypes as $t) {
                $count = $rawData->where('date', $ds)->where('react_type_id', $t->id)->first()?->count ?? 0;
                $datasets[$t->id]['data']->push($count);
            }
            $current->addDay();
        }

        return response()->json([
            'labels'   => $dates,
            'datasets' => $datasets->values(),
        ]);
    }

    // ── AJAX: By Department ──────────────────────────────────────────────
    public function byDepartment(Request $request)
    {
        $companyId = $this->getCompanyId();
        $dateFrom  = $request->query('date_from', today()->toDateString());
        $dateTo    = $request->query('date_to', today()->toDateString());
        $hourFrom  = $request->query('hour_from');
        $hourTo    = $request->query('hour_to');

        $reactTypes = ReactType::active()->get();

        $rawCounts = ReactLog::byCompany($companyId)
            ->byDateRange($dateFrom, $dateTo)->byHourRange($hourFrom, $hourTo)
            ->whereNotNull('department_id')
            ->select('department_id', 'react_type_id', DB::raw('COUNT(*) as count'))
            ->groupBy('department_id', 'react_type_id')
            ->get()->groupBy('department_id');

        $departments = Department::where('company_id', $companyId)->active()->get()
            ->map(function ($dept) use ($reactTypes, $rawCounts) {
                $dc    = $rawCounts->get($dept->id, collect());
                $total = $dc->sum('count');
                return [
                    'name'         => $dept->name,
                    'total_reacts' => $total,
                    'breakdown'    => $reactTypes->map(fn($t) => [
                        'type'  => $t->type,
                        'icon'  => $t->icon_code,
                        'color' => $this->colorFor($t->id),
                        'count' => $dc->where('react_type_id', $t->id)->first()?->count ?? 0,
                    ]),
                ];
            });

        return response()->json($departments);
    }

    // ── GET: Sections for a department (dropdown) ────────────────────────
    public function sections(Request $request)
    {
        $departmentId = $request->query('department_id');
        $sections = Section::where('department_id', $departmentId)->active()->get(['id', 'name']);
        return response()->json($sections);
    }

    // ── POST: Add Department ─────────────────────────────────────────────
    public function storeDepartment(Request $request)
    {
        $request->validate(['name' => 'required|string|max:200']);
        $dept = Department::create([
            'company_id' => $this->getCompanyId(),
            'name'       => $request->name,
            'is_active'  => true,
        ]);
        return response()->json(['success' => true, 'department' => $dept]);
    }

    // ── POST: Add Section ────────────────────────────────────────────────
    public function storeSection(Request $request)
    {
        $request->validate([
            'name'          => 'required|string|max:200',
            'department_id' => 'required|exists:departments,id',
        ]);
        $section = Section::create([
            'department_id' => $request->department_id,
            'name'          => $request->name,
            'is_active'     => true,
        ]);
        return response()->json(['success' => true, 'section' => $section]);
    }

    // ── GET: List Departments ────────────────────────────────────────────
    public function departmentsIndex()
    {
        $companyId = $this->getCompanyId();
        $departments = Department::where('company_id', $companyId)->orderBy('id', 'desc')->get();
        return response()->json($departments);
    }

    // ── PUT: Update Department ───────────────────────────────────────────
    public function updateDepartment(Request $request, Department $department)
    {
        if ($department->company_id !== $this->getCompanyId()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $request->validate(['name' => 'required|string|max:200']);
        $department->update(['name' => $request->name]);

        return response()->json(['success' => true]);
    }

    // ── DELETE: Destroy Department ────────────────────────────────────────
    public function destroyDepartment(Department $department)
    {
        if ($department->company_id !== $this->getCompanyId()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $department->delete();

        return response()->json(['success' => true]);
    }

    // ── PATCH: Toggle Department Status ──────────────────────────────────
    public function toggleDepartmentStatus(Department $department)
    {
        if ($department->company_id !== $this->getCompanyId()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $department->update(['is_active' => !$department->is_active]);

        return response()->json(['success' => true, 'is_active' => $department->is_active]);
    }

    // ── GET: List Sections ───────────────────────────────────────────────
    public function sectionsIndex()
    {
        $companyId = $this->getCompanyId();
        $sections = Section::whereHas('department', function($q) use ($companyId) {
            $q->where('company_id', $companyId);
        })->with('department')->orderBy('id', 'desc')->get();

        return response()->json($sections);
    }

    // ── PUT: Update Section ──────────────────────────────────────────────
    public function updateSection(Request $request, Section $section)
    {
        $companyId = $this->getCompanyId();
        if ($section->department->company_id !== $companyId) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'name'          => 'required|string|max:200',
            'department_id' => 'required|exists:departments,id',
        ]);

        $newDept = Department::find($request->department_id);
        if ($newDept->company_id !== $companyId) {
            return response()->json(['success' => false, 'message' => 'Unauthorized department choice'], 403);
        }

        $section->update([
            'name'          => $request->name,
            'department_id' => $request->department_id
        ]);

        return response()->json(['success' => true]);
    }

    // ── DELETE: Destroy Section ──────────────────────────────────────────
    public function destroySection(Section $section)
    {
        if ($section->department->company_id !== $this->getCompanyId()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $section->delete();

        return response()->json(['success' => true]);
    }

    // ── PATCH: Toggle Section Status ─────────────────────────────────────
    public function toggleSectionStatus(Section $section)
    {
        if ($section->department->company_id !== $this->getCompanyId()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $section->update(['is_active' => !$section->is_active]);

        return response()->json(['success' => true, 'is_active' => $section->is_active]);
    }

    // ── GET: Excel Export ────────────────────────────────────────────────
    public function export(Request $request)
    {
        $companyId    = $this->getCompanyId();
        $departmentId = $request->query('department_id') ? (int) $request->query('department_id') : null;
        $sectionId    = $request->query('section_id')    ? (int) $request->query('section_id')    : null;
        $dateFrom     = $request->query('date_from', today()->toDateString());
        $dateTo       = $request->query('date_to',   today()->toDateString());
        $hourFrom     = $request->query('hour_from') ?: null;
        $hourTo       = $request->query('hour_to')   ?: null;
        $filename     = 'react_report_' . $dateFrom . '_to_' . $dateTo . '.xlsx';

        return Excel::download(
            new ReactLogsExport(
                $companyId,
                $departmentId,
                $sectionId,
                $dateFrom,
                $dateTo,
                $hourFrom,
                $hourTo
            ),
            $filename,
            \Maatwebsite\Excel\Excel::XLSX,
            ['Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']
        );
    }

    // ── AJAX: Logs Table ──────────────────────────────────────────────────
    public function logs(Request $request)
    {
        $companyId    = $this->getCompanyId();
        $departmentId = $request->query('department_id') ? (int) $request->query('department_id') : null;
        $sectionId    = $request->query('section_id')    ? (int) $request->query('section_id')    : null;
        $dateFrom     = $request->query('date_from', today()->toDateString());
        $dateTo       = $request->query('date_to',   today()->toDateString());
        $hourFrom     = $request->query('hour_from') ?: null;
        $hourTo       = $request->query('hour_to')   ?: null;

        $logs = ReactLog::with(['user', 'department', 'section', 'reactType'])
            ->byCompany($companyId)
            ->byDepartment($departmentId)
            ->bySection($sectionId)
            ->byDateRange($dateFrom, $dateTo)
            ->byHourRange($hourFrom, $hourTo)
            ->latest()
            ->paginate(15);

        // Fetch corresponding option submissions efficiently
        $userIds = $logs->pluck('user_id')->unique();
        $submissions = OptionSubmission::whereIn('user_id', $userIds)
            ->byCompany($companyId)
            ->byDateRange($dateFrom, $dateTo)
            ->with('options')
            ->latest()
            ->get()
            ->groupBy(function ($sub) {
                return $sub->user_id . '_' . $sub->created_at->toDateString();
            });

        $logs->getCollection()->transform(function ($log) use ($submissions) {
            $key = $log->user_id . '_' . $log->created_at->toDateString();
            $sub = $submissions->get($key)?->first();
            $log->selected_options = $sub
                ? ($sub->options->pluck('name')->implode(', ') ?: 'No Selection')
                : null;
            return $log;
        });

        return response()->json($logs);
    }

    // ── Helper ───────────────────────────────────────────────────────────
    private function colorFor(int $id): string
    {
        return match($id) {
            1 => '#22c55e',
            2 => '#3b82f6',
            3 => '#eab308',
            4 => '#f97316',
            5 => '#ef4444',
            default => '#6b7280',
        };
    }

    // ═══════════════════════════════════════════════════════════════════════
    // OPTIONS RESULTS — ANALYTICS
    // ═══════════════════════════════════════════════════════════════════════

    /**
     * AJAX: Options Results — Summary Stats
     * GET /options-results/stats
     */
    public function optionStats(Request $request)
    {
        $companyId    = $this->getCompanyId();
        $departmentId = $request->query('department_id') ? (int)$request->query('department_id') : null;
        $sectionId    = $request->query('section_id')    ? (int)$request->query('section_id')    : null;
        $dateFrom     = $request->query('date_from', today()->toDateString());
        $dateTo       = $request->query('date_to',   today()->toDateString());

        $base = OptionSubmission::byCompany($companyId)
            ->byDepartment($departmentId)
            ->bySection($sectionId)
            ->byDateRange($dateFrom, $dateTo);

        $totalSubmissions = (clone $base)->count();

        // No-selection: submissions with zero items
        $noSelectionCount = (clone $base)
            ->whereDoesntHave('items')
            ->count();

        // Participation: unique employees who submitted
        $uniqueSubmitters = (clone $base)->distinct('user_id')->count('user_id');

        // Total employees in scope
        $empQuery = User::active()->byCompany($companyId);
        if ($departmentId) $empQuery->where('department_id', $departmentId);
        if ($sectionId)    $empQuery->where('section_id', $sectionId);
        $totalEmployees   = $empQuery->count();
        $participationPct = $totalEmployees > 0
            ? round(($uniqueSubmitters / $totalEmployees) * 100, 1)
            : 0;

        // Per-option counts
        $options = Option::where('is_active', true)->orderBy('id')->get();
        $optionCounts = $options->map(function ($opt) use ($base, $totalSubmissions) {
            $count = (clone $base)
                ->whereHas('items', fn($q) => $q->where('option_id', $opt->id))
                ->count();

            return [
                'id'         => $opt->id,
                'name'       => $opt->name,
                'count'      => $count,
                'percentage' => $totalSubmissions > 0 ? round(($count / $totalSubmissions) * 100, 1) : 0,
            ];
        });

        $topOption = $totalSubmissions > 0 ? $optionCounts->sortByDesc('count')->first() : null;
        if ($topOption && $topOption['count'] === 0) {
            $topOption = null;
        }

        return response()->json([
            'total_submissions'  => $totalSubmissions,
            'no_selection_count' => $noSelectionCount,
            'unique_submitters'  => $uniqueSubmitters,
            'total_employees'    => $totalEmployees,
            'participation_pct'  => $participationPct,
            'option_counts'      => $optionCounts,
            'top_option'         => $topOption,
        ]);
    }
}
