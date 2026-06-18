<?php

namespace App\Exports;

use App\Models\ReactLog;
use App\Models\OptionSubmission;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ReactLogsExport implements FromQuery, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected $submissions = null;

    public function __construct(
        protected int    $companyId,
        protected ?int   $departmentId,
        protected ?int   $sectionId,
        protected string $dateFrom,
        protected string $dateTo,
        protected ?string $hourFrom = null,
        protected ?string $hourTo   = null
    ) {}

    protected function getSubmissions()
    {
        if ($this->submissions === null) {
            $this->submissions = OptionSubmission::where('company_id', $this->companyId)
                ->byDepartment($this->departmentId)
                ->bySection($this->sectionId)
                ->byDateRange($this->dateFrom, $this->dateTo)
                ->with('options')
                ->latest()
                ->get()
                ->groupBy(function ($sub) {
                    return $sub->user_id . '_' . $sub->created_at->toDateString();
                });
        }
        return $this->submissions;
    }

    public function query()
    {
        return ReactLog::query()
            ->with(['user', 'reactType', 'department', 'section'])
            ->byCompany($this->companyId)
            ->byDepartment($this->departmentId)
            ->bySection($this->sectionId)
            ->byDateRange($this->dateFrom, $this->dateTo)
            ->byHourRange($this->hourFrom, $this->hourTo)
            ->oldest();
    }

    public function headings(): array
    {
        return [
            '#',
            'Employee Name',
            'Username',
            'Employee ID',
            'Department',
            'Section',
            'Emoji Reaction',
            'Selected Options',
            'Note',
            'Date',
            'Time (Hour)',
            'IP Address',
            'Device Info',
        ];
    }

    public function map($log): array
    {
        $key = $log->user_id . '_' . $log->created_at->toDateString();
        $sub = $this->getSubmissions()->get($key)?->first();
        $options = $sub
            ? ($sub->options->pluck('name')->implode(', ') ?: 'No Selection')
            : '—';

        return [
            $log->id,
            $log->user->name         ?? 'N/A',
            $log->user->username     ?? 'N/A',
            $log->user->employee_id  ?? 'N/A',
            $log->department->name   ?? 'N/A',
            $log->section->name      ?? 'N/A',
            $log->reactType->type    ?? 'N/A',
            $options,
            $log->note,
            $log->created_at->format('Y-m-d'),
            $log->created_at->format('H:i:s'),
            $log->ip_address,
            $log->device_info,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            // Bold header row
            1 => ['font' => ['bold' => true]],
        ];
    }
}
