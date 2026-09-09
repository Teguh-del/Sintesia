<?php

namespace App\Policies;

use App\Models\CommodityRequest;
use App\Models\User;

class CommodityRequestPolicy
{
    public function create(User $user): bool
    {
        return $user->isPengepul() || $user->isKonsumen() || $user->isAdmin();
    }

    public function update(User $user, CommodityRequest $request): bool
    {
        return $request->user_id === $user->id || $user->isAdmin();
    }

    public function delete(User $user, CommodityRequest $request): bool
    {
        return $request->user_id === $user->id || $user->isAdmin();
    }

    public function view(User $user, CommodityRequest $request): bool
    {
        return true;
    }

    public function acceptOffer(User $user, CommodityRequest $request): bool
    {
        return $request->user_id === $user->id || $user->isAdmin();
    }

    public function offer(User $user, CommodityRequest $request): bool
    {
        return ($user->isPetani() || $user->isAdmin()) 
            && in_array($request->status, ['Aktif', 'Mendapat Penawaran']);
    }
}
