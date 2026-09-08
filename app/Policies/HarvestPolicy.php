<?php

namespace App\Policies;

use App\Models\Harvest;
use App\Models\User;

class HarvestPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isPetani();
    }

    public function view(User $user, Harvest $harvest): bool
    {
        return $user->id === $harvest->user_id;
    }

    public function create(User $user): bool
    {
        return $user->isPetani();
    }

    public function update(User $user, Harvest $harvest): bool
    {
        return $user->id === $harvest->user_id;
    }

    public function delete(User $user, Harvest $harvest): bool
    {
        return $user->id === $harvest->user_id || $user->isAdmin();
    }
}
