<?php

namespace App\Services;

use App\Repositories\ProductRepository;
use App\Models\Product;
use App\Models\Segment;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

class ProductService
{
    protected $productRepository;

    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    public function getProductsByUser($userId)
    {
        return $this->productRepository->getByUser($userId);
    }

    public function getAllProducts()
    {
        return $this->productRepository->all();
    }

    public function getProductWithRelations($id)
    {
        return $this->productRepository->findWithRelations($id);
    }

    public function getProductById($id)
    {
        return $this->productRepository->find($id);
    }

    public function createProduct(array $data, ?array $images = null)
    {
        // 1️⃣ Create the product first (without images)
        $product = $this->productRepository->create($data);

        // 2️⃣ Handle uploaded images (store in S3)
        if ($images && count($images) > 0) {
            foreach ($images as $image) {
                if ($image instanceof \Illuminate\Http\UploadedFile) {

                    // Store image in S3 under 'products_images' folder
                    $path = $image->store('products_images', [
                        'disk' => 's3',
                        'visibility' => 'public',
                    ]);

                    // Save record in product_images table
                    $product->images()->create([
                        'image' => $path, // store the S3 key/path
                    ]);
                }
            }
        }

        return $product;
    }


    public function updateProduct(Product $product, array $data, ?array $images = null, ?array $deleteGalleryIds = null)
    {
        return DB::transaction(function () use ($product, $data, $images, $deleteGalleryIds) {

            // 1️⃣ Handle Main Image (Single Column 'image')
            // Only run this if you have a specific file input named 'image' for a cover photo
            if (!empty($images['main']) && $images['main'] instanceof UploadedFile) {
                // Delete old main image from S3
                if ($product->image && Storage::disk('s3')->exists($product->image)) {
                    Storage::disk('s3')->delete($product->image);
                }

                // Store new main image directly to S3 (Public)
                $data['image'] = $images['main']->storePublicly('products', 's3');
            }

            // 2️⃣ Handle Deletion of Gallery Images
            if (!empty($deleteGalleryIds)) {
                // Find the image records
                $imagesToDelete = $product->images()->whereIn('id', $deleteGalleryIds)->get();

                foreach ($imagesToDelete as $img) {
                    // Delete actual file from S3
                    if ($img->image && Storage::disk('s3')->exists($img->image)) {
                        Storage::disk('s3')->delete($img->image);
                    }
                }
                // Delete database records
                $product->images()->whereIn('id', $deleteGalleryIds)->delete();
            }

            // 3️⃣ Handle New Gallery Images
            if (!empty($images['gallery'])) {
                // Calculate remaining slots (Max 8)
                $currentCount = $product->images()->count();
                $remainingSlots = max(0, 8 - $currentCount);

                // Slice the array to prevent over-uploading
                $filesToUpload = array_slice($images['gallery'], 0, $remainingSlots);

                foreach ($filesToUpload as $img) {
                    if ($img instanceof UploadedFile) {
                        // Upload to S3 (Public)
                        $path = $img->storePublicly('products', 's3');

                        // Create DB Record
                        $product->images()->create(['image' => $path]);
                    }
                }
            }

            // 4️⃣ Update other product fields (Name, Price, etc.)
            // If you don't have a Repository, use: $product->update($data);
            $this->productRepository->update($product, $data);

            return $product;
        });
    }


    public function deleteProduct(Product $product)
    {
        // Add business logic here if needed
        return $this->productRepository->delete($product);
    }

    public function getApprovedProductsBySegment(Segment $segment, ?int $categoryId = null, ?int $barangayId = null)
    {
        return $this->productRepository->getApproveProducts($segment, $categoryId, $barangayId);
    }

    public function getProductsByStatusPaginated(string $status, int $perPage = 10)
    {
        return $this->productRepository->getByStatusPaginated($status, $perPage);
    }

    public function getMoreProductsByUser($userId, $excludeProductId, $limit = 6)
    {
        return $this->productRepository->getMoreByUser($userId, $excludeProductId, $limit);
    }
}
