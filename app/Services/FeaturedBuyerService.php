<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class FeaturedBuyerService {
    public function store($data, $repository) {
        // Handle Avatar Upload to S3
        if (isset($data['avatar'])) {
            $data['avatar_path'] = $data['avatar']->storePublicly('featured/avatars', 's3');
        }

        $buyer = $repository->create($data + ['user_id' => Auth::id()]);

        // Handle Multiple Items & S3 Uploads
        $items = [];
        foreach ($data['items'] as $itemData) {
            $path = $itemData['image']->storePublicly('featured/items', 's3');
            $items[] = [
                'product_name'   => $itemData['product_name'],
                'price'          => $itemData['price'],
                'image_path'     => $path,
                'size'           => $itemData['size'] ?? null,
                'category_label' => $itemData['category_label'] ?? null,
            ];
        }

        return $repository->createItems($buyer, $items);
    }
}