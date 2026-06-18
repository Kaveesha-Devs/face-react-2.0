<?php

namespace App\Policies;

use App\Models\ReactLog;
use App\Models\User;

class ReactLogPolicy
{
    public function export(User $user): bool
    {
        return $user->hasAdminAccess();
    }
}