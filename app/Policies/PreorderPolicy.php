<?php

namespace App\Policies;

use App\Models\Preorder;
use App\Models\User;

class PreorderPolicy
{
    public function create(User $user): bool
    {
        return $user->isPetani() || $user->isAdmin();
    }

    public function update(User $user, Preorder $preorder): bool
    {
        return $preorder->user_id === $user->id || $user->isAdmin();
    }

    public function delete(User $user, Preorder $preorder): bool
    {
        return $preorder->user_id === $user->id || $user->isAdmin();
    }

    public function book(User $user, Preorder $preorder): bool
    {
        return $preorder->user_id !== $user->id 
            && in_array($preorder->status, ['Dibuka', 'Menunggu Panen']);
    }
}
