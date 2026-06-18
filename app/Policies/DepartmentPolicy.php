<?php

namespace App\Policies;

use App\Models\Department;
use App\Models\User;

class DepartmentPolicy
{
    public function view(User $user, Department $department): bool
    {
        return $user->hasAdminAccess()
            && $user->company_id === $department->company_id;
    }

    public function update(User $user, Department $department): bool
    {
        return $user->hasAdminAccess()
            && $user->company_id === $department->company_id;
    }

    public function delete(User $user, Department $department): bool
    {
        return $user->hasAdminAccess()
            && $user->company_id === $department->company_id;
    }
}