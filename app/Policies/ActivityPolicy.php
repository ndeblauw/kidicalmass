<?php

namespace App\Policies;

use App\Models\Activity;
use App\Models\User;

class ActivityPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Activity $activity): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Activity $activity): bool
    {
        return true;
    }

    public function delete(User $user, Activity $activity): bool
    {
        return true;
    }

    public function restore(User $user, Activity $activity): bool
    {
        return true;
    }

    public function forceDelete(User $user, Activity $activity): bool
    {
        return true;
    }
}
