<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Segment;
use App\Repositories\ProductRepository;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

class ProductService
{
    protected $productRepository;

    public function __construct(
        ProductRepository $productRepository,
        private readonly FileStorageService $files
    ) {
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
        foreach ($this->files->uploadPublicMany($images, 'products_images') as $path) {
            // Save record in product_images table
            $product->images()->create([
                'image' => $path, // store the S3 key/path
            ]);
        }

        return $product;
    }

    public function updateProduct(Product $product, array $data, ?array $images = null, ?array $deleteGalleryIds = null)
    {
        return DB::transaction(function () use ($product, $data, $images, $deleteGalleryIds) {

            // 1️⃣ Handle Main Image (Single Column 'image')
            // Only run this if you have a specific file input named 'image' for a cover photo
            if (! empty($images['main']) && $images['main'] instanceof UploadedFile) {
                $data['image'] = $this->files->replacePublic($product->image, $images['main'], 'products');
            }

            // 2️⃣ Handle Deletion of Gallery Images
            if (! empty($deleteGalleryIds)) {
                // Find the image records
                $imagesToDelete = $product->images()->whereIn('id', $deleteGalleryIds)->get();

                $this->files->deleteManyIfExists($imagesToDelete->pluck('image'));

                // Delete database records
                $product->images()->whereIn('id', $deleteGalleryIds)->delete();
            }

            // 3️⃣ Handle New Gallery Images
            if (! empty($images['gallery'])) {
                // Calculate remaining slots (Max 8)
                $currentCount = $product->images()->count();
                $remainingSlots = max(0, 8 - $currentCount);

                // Slice the array to prevent over-uploading
                $filesToUpload = array_slice($images['gallery'], 0, $remainingSlots);

                foreach ($this->files->uploadPublicMany($filesToUpload, 'products') as $path) {
                    // Create DB Record
                    $product->images()->create(['image' => $path]);
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
        $product->loadMissing('images');

        $this->files->deleteIfExists($product->image);
        $this->files->deleteIfExists($product->qr_code);
        $this->files->deleteManyIfExists($product->images->pluck('image'));

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
