<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Department;
use App\Models\ReactLog;
use App\Models\ReactType;
use App\Models\Section;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SampleDataSeeder extends Seeder
{
    public function run(): void
    {
        // ── Get company ──────────────────────────────────────────────────
        $company = Company::where('name', 'EravTech')->first();
        if (!$company) {
            $this->command->error('EravTech company not found! Run AdminUserSeeder first.');
            return;
        }

        $this->command->info("Using company: {$company->name} (ID: {$company->id})");

        // ── Departments & Sections ───────────────────────────────────────
        $deptData = [
            'Operations'  => ['Production', 'Logistics', 'Quality Control'],
            'Finance'     => ['Payroll', 'Accounts', 'Budgeting'],
            'HR'          => ['Recruitment', 'Training', 'Welfare'],
            'IT'          => ['Development', 'Support', 'Infrastructure'],
            'Sales'       => ['Local Sales', 'Export Sales'],
        ];

        $departments = [];
        $sections    = [];

        foreach ($deptData as $deptName => $sectionNames) {
            $dept = Department::firstOrCreate(
                ['company_id' => $company->id, 'name' => $deptName],
                ['is_active' => true]
            );
            $departments[$deptName] = $dept;

            foreach ($sectionNames as $secName) {
                $sec = Section::firstOrCreate(
                    ['department_id' => $dept->id, 'name' => $secName],
                    ['is_active' => true]
                );
                $sections[] = ['dept' => $dept, 'section' => $sec];
            }
        }

        $this->command->info('✅ Departments & Sections created');

        // ── Employees ────────────────────────────────────────────────────
        $employeeData = [
            // [name, username, dept_name, section_name]
            ['Kamal Perera',    'kamal.p',    'Operations', 'Production'],
            ['Nimal Silva',     'nimal.s',    'Operations', 'Production'],
            ['Saman Fernando',  'saman.f',    'Operations', 'Logistics'],
            ['Ruwan Bandara',   'ruwan.b',    'Operations', 'Logistics'],
            ['Chamara Rajapaksa','chamara.r', 'Operations', 'Quality Control'],
            ['Dilani Wickrama',  'dilani.w',  'Finance',    'Payroll'],
            ['Nadee Jayasuriya', 'nadee.j',   'Finance',    'Accounts'],
            ['Prashani Kumari',  'prashani.k','Finance',    'Budgeting'],
            ['Ishara Madushani', 'ishara.m',  'HR',         'Recruitment'],
            ['Tharaka Dissanayake','tharaka.d','HR',        'Training'],
            ['Sachini Perera',   'sachini.p', 'HR',         'Welfare'],
            ['Lasith Malinga',   'lasith.m',  'IT',         'Development'],
            ['Dinuka Thilakarathna','dinuka.t','IT',        'Development'],
            ['Asitha Nanayakkara', 'asitha.n', 'IT',        'Support'],
            ['Kasun Rathnayake',  'kasun.r',  'IT',         'Infrastructure'],
            ['Supun Jayawardena', 'supun.j',  'Sales',      'Local Sales'],
            ['Malsha Gunasekara', 'malsha.g', 'Sales',      'Local Sales'],
            ['Thilini Herath',    'thilini.h','Sales',      'Export Sales'],
            ['Hasini Ranatunga',  'hasini.r', 'Sales',      'Export Sales'],
            ['Gayan Samarawickrama','gayan.s','Operations', 'Quality Control'],
        ];

        $employees = [];
        foreach ($employeeData as $i => [$name, $username, $deptName, $secName]) {
            $dept    = $departments[$deptName];
            $section = Section::where('department_id', $dept->id)->where('name', $secName)->first();

            $user = User::updateOrCreate(
                ['username' => $username],
                [
                    'name'          => $name,
                    'email'         => str_replace('.', '_', $username) . '@eravtech.com',
                    'password'      => Hash::make('password123'),
                    'company_id'    => $company->id,
                    'department_id' => $dept->id,
                    'section_id'    => $section?->id,
                    'employee_id'   => 'ERT' . str_pad($i + 1, 4, '0', STR_PAD_LEFT),
                    'role'          => 'employee',
                    'is_active'     => true,
                ]
            );
            $employees[] = $user;
        }

        $this->command->info('✅ ' . count($employees) . ' employees created');

        // ── React Logs — past 30 days ─────────────────────────────────────
        $reactTypes = ReactType::active()->get();

        // Weighted probabilities: Excellent=40%, Good=30%, Average=15%, Poor=10%, VeryPoor=5%
        $weights = [
            1 => 40, // Excellent
            2 => 30, // Good
            3 => 15, // Average
            4 => 10, // Poor
            5 => 5,  // Very Poor
        ];

        // Build weighted array
        $weightedTypeIds = [];
        foreach ($weights as $typeId => $weight) {
            for ($i = 0; $i < $weight; $i++) {
                $weightedTypeIds[] = $typeId;
            }
        }

        // Delete existing sample logs for this company (clean slate)
        ReactLog::where('company_id', $company->id)->delete();

        $logsCreated = 0;
        $now = Carbon::now();

        // Generate logs for past 30 days
        for ($day = 29; $day >= 0; $day--) {
            $date = $now->copy()->subDays($day)->format('Y-m-d');

            // Skip some days randomly (weekends ~30% skip)
            $dayOfWeek = Carbon::parse($date)->dayOfWeek;
            $isWeekend = in_array($dayOfWeek, [0, 6]); // 0=Sun, 6=Sat

            foreach ($employees as $employee) {
                // Skip weekends 40% of time, weekdays 10% of time
                $skipChance = $isWeekend ? 40 : 10;
                if (rand(1, 100) <= $skipChance) continue;

                $typeId = $weightedTypeIds[array_rand($weightedTypeIds)];

                // Random hour between 7am - 10am (morning check-in)
                $hour   = rand(7, 10);
                $minute = rand(0, 59);

                ReactLog::create([
                    'user_id'       => $employee->id,
                    'react_type_id' => $typeId,
                    'company_id'    => $company->id,
                    'department_id' => $employee->department_id,
                    'section_id'    => $employee->section_id,
                    'note'          => null,
                    'ip_address'    => '192.168.1.' . rand(10, 100),
                    'device_info'   => 'SampleApp/1.0',
                    'created_at'    => $date . ' ' . str_pad($hour, 2, '0', STR_PAD_LEFT) . ':' . str_pad($minute, 2, '0', STR_PAD_LEFT) . ':00',
                    'updated_at'    => $date . ' ' . str_pad($hour, 2, '0', STR_PAD_LEFT) . ':' . str_pad($minute, 2, '0', STR_PAD_LEFT) . ':00',
                ]);
                $logsCreated++;
            }
        }

        $this->command->info("✅ {$logsCreated} sample react logs created (30 days)");
        $this->command->info('');
        $this->command->info('🎉 Sample data complete! Open http://localhost:8000/dashboard to see it.');
    }
}
