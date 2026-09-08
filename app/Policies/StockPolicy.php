<?php

namespace App\Policies;

use App\Models\Stock;
use App\Models\User;

class StockPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isPetani();
    }

    public function view(User $user, Stock $stock): bool
    {
        return $user->id === $stock->user_id;
    }

    public function update(User $user, Stock $stock): bool
    {
        return $user->id === $stock->user_id;
    }

    public function delete(User $user, Stock $stock): bool
    {
        return $user->id === $stock->user_id || $user->isAdmin();
    }
}
