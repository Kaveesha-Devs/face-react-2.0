<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Exports\ReactLogsExport;
use App\Models\ReactLog;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ExportController extends Controller
{
    use \App\Traits\ApiResponse;

    /**
     * Export react logs to Excel
     * GET /api/export
     * Query params: date_from, date_to, department_id, section_id, hour_from, hour_to
     */
    public function export(Request $request): BinaryFileResponse
    {
        $user         = $request->user();
        $companyId    = $user->company_id;
        $departmentId = $request->query('department_id');
        $sectionId    = $request->query('section_id');
        $dateFrom     = $request->query('date_from', today()->toDateString());
        $dateTo       = $request->query('date_to', today()->toDateString());
        $hourFrom     = $request->query('hour_from');   // e.g. 8  (8 AM)
        $hourTo       = $request->query('hour_to');     // e.g. 17 (5 PM)

        $filename = 'react_report_' . $dateFrom . '_to_' . $dateTo . '.xlsx';

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
            $filename
        );
    }
}