<?php

namespace App\Policies;

use App\Models\PriceOffer;
use App\Models\User;

class PriceOfferPolicy
{
    public function view(User $user, PriceOffer $offer): bool
    {
        return $offer->buyer_id === $user->id 
            || $offer->seller_id === $user->id 
            || $user->isAdmin();
    }

    public function accept(User $user, PriceOffer $offer): bool
    {
        // Farmer can accept buyer's initial offer ('Menunggu')
        if ($offer->seller_id === $user->id && $offer->status === 'Menunggu') {
            return true;
        }

        // Buyer can accept farmer's counter offer ('Counter Offer')
        if ($offer->buyer_id === $user->id && $offer->status === 'Counter Offer') {
            return true;
        }

        return false;
    }

    public function counter(User $user, PriceOffer $offer): bool
    {
        return $offer->seller_id === $user->id && $offer->status === 'Menunggu';
    }

    public function reject(User $user, PriceOffer $offer): bool
    {
        return ($offer->seller_id === $user->id || $offer->buyer_id === $user->id || $user->isAdmin())
            && !in_array($offer->status, ['Selesai', 'Ditolak', 'Dibatalkan']);
    }
}
