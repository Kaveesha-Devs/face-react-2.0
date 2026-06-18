<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function view(User $authUser, User $targetUser): bool
    {
        return $authUser->hasAdminAccess()
            && $authUser->company_id === $targetUser->company_id;
    }

    public function update(User $authUser, User $targetUser): bool
    {
        if ($authUser->hasAdminAccess()) {
            return $authUser->company_id === $targetUser->company_id;
        }
        return $authUser->id === $targetUser->id;
    }

    public function delete(User $authUser, User $targetUser): bool
    {
        return $authUser->hasAdminAccess()
            && $authUser->company_id === $targetUser->company_id
            && $authUser->id !== $targetUser->id;
    }
}