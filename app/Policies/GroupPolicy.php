<?php

namespace App\Policies;

use App\Models\Group;
use App\Models\User;

class GroupPolicy
{
    public function view(User $user, Group $group): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return $user->isCaptainOf($group);
    }
}
