<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        // Create company if not exists
        $company = Company::firstOrCreate(
            ['email' => 'admin@eravtech.com'],
            [
                'name'      => 'EravTech',
                'is_active' => true,
            ]
        );

        // Create or update admin user
        User::updateOrCreate(
            ['username' => 'eravtech'],
            [
                'name'       => 'EravTech Admin',
                'email'      => 'admin@eravtech.com',
                'password'   => Hash::make('Erav1234'),
                'company_id' => $company->id,
                'role'       => User::ROLE_ADMIN,
                'is_active'  => true,
            ]
        );

        $this->command->info('✅ Admin user seeded! username: eravtech / password: Erav1234');
    }
}
