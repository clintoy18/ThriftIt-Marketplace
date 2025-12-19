<?php

namespace App\Repositories;

use App\Models\FeaturedBuyer;

class FeaturedBuyerRepository {
    public function create(array $data) {
        return FeaturedBuyer::create($data);
    }

    public function createItems(FeaturedBuyer $buyer, array $items) {
        return $buyer->items()->createMany($items);
    }
    
    public function getForUser($userId) {
        return FeaturedBuyer::with('items')->where('user_id', $userId)->get();
    }
}