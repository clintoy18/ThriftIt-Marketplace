<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;

class FeaturedBuyerService
{
    public function __construct(
        private readonly FileStorageService $files
    ) {}

    public function store($data, $repository)
    {
        // Handle Avatar Upload to S3
        if (isset($data['avatar'])) {
            $data['avatar_path'] = $this->files->uploadPublic($data['avatar'], 'featured/avatars');
        }

        $buyer = $repository->create($data + ['user_id' => Auth::id()]);

        // Handle Multiple Items & S3 Uploads
        $items = [];
        foreach ($data['items'] as $itemData) {
            $path = $this->files->uploadPublic($itemData['image'], 'featured/items');
            $items[] = [
                'product_name' => $itemData['product_name'],
                'price' => $itemData['price'],
                'image_path' => $path,
                'size' => $itemData['size'] ?? null,
                'category_label' => $itemData['category_label'] ?? null,
            ];
        }

        return $repository->createItems($buyer, $items);
    }
}
