<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;

class OrderPolicy
{
    /**
     * Determine whether the user can view the order.
     */
    public function view(User $user, Order $order): bool
    {
        return $order->buyer_id === $user->id 
            || $order->seller_id === $user->id 
            || $user->isAdmin();
    }

    /**
     * Determine whether the user (farmer/seller) can confirm the order.
     */
    public function confirm(User $user, Order $order): bool
    {
        return $order->seller_id === $user->id && $order->status === 'Menunggu Konfirmasi';
    }

    /**
     * Determine whether the user (farmer/seller) can process the order.
     */
    public function process(User $user, Order $order): bool
    {
        return $order->seller_id === $user->id && $order->status === 'Dikonfirmasi';
    }

    /**
     * Determine whether the user can complete the order.
     */
    public function complete(User $user, Order $order): bool
    {
        return ($order->buyer_id === $user->id || $order->seller_id === $user->id || $user->isAdmin())
            && $order->status === 'Diproses';
    }

    /**
     * Determine whether the user can cancel/reject the order.
     */
    public function cancel(User $user, Order $order): bool
    {
        if (in_array($order->status, ['Selesai', 'Dibatalkan'])) {
            return false;
        }

        if ($order->buyer_id === $user->id) {
            return $order->status === 'Menunggu Konfirmasi';
        }

        if ($order->seller_id === $user->id || $user->isAdmin()) {
            return true;
        }

        return false;
    }
}
