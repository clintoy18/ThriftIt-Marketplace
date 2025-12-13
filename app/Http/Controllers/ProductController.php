<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Product;
use App\Models\Categories;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use App\Services\ProductService;
use App\Services\CategoriesService;
use App\Models\Segment;
use App\Models\Barangay;
use App\Models\Image;
use Illuminate\Support\Arr;

class ProductController extends Controller
{
    protected $productService, $categoryService;

    public function __construct(ProductService $productService, CategoriesService $categoryService)
    {
        $this->productService = $productService;
        $this->categoryService = $categoryService;
        $this->middleware('subscribed')->only(['create', 'store']);
        $this->middleware('verified.seller')->only(['qrStep', 'storeQr']);
    }

    /**
     * STEP 1: Save product details to session
     */
    public function storeStep1(StoreProductRequest $request)
    {
        $validated = $request->validated();
        $validated['user_id'] = Auth::id();
        $validated['approval_status'] = Auth::user()->is_verified ? 'approved' : 'pending';

        // 1. Save Text Data (exclude images and removed_images from this part)
        $dataToSession = \Illuminate\Support\Arr::except($validated, ['images', 'image', 'removed_images']);
        session(['product_step1' => $dataToSession]);

        // 2. Handle Image Management
        // Get current list of images from session
        $currentSessionImages = session('product_images', []);

        // A. DELETE REMOVED IMAGES FROM S3
        if ($request->has('removed_images')) {
            $removedPaths = $request->input('removed_images');

            foreach ($removedPaths as $path) {
                // Delete from S3 if it exists
                if (Storage::disk('s3')->exists($path)) {
                    Storage::disk('s3')->delete($path);
                }
            }

            // Remove these paths from our PHP array so they don't get saved back to session
            $currentSessionImages = array_filter($currentSessionImages, function ($path) use ($removedPaths) {
                return !in_array($path, $removedPaths);
            });
        }

        // B. Add Newly Uploaded Images
        if ($request->hasFile('images')) {
            $newImages = [];
            foreach ($request->file('images') as $img) {
                // Upload to S3 (Temp Folder) with public visibility
                $newImages[] = $img->storePublicly('temp_products', 's3');
            }
            // Merge kept old images + new images
            $currentSessionImages = array_merge($currentSessionImages, $newImages);
        }

        // 3. Save updated list back to session (array_values re-indexes the array keys 0,1,2...)
        session(['product_images' => array_values($currentSessionImages)]);

        // 4. Redirect
        if (Auth::user()->is_verified) {
            return redirect()->route('sell-item.qr-step')
                ->with('success', 'Step 1 complete. Proceed to QR upload.');
        }

        return redirect()->route('sell-item.final-step')
            ->with('success', 'Step 1 complete. Review your product.');
    }

    /**
     * STEP 2: Show optional QR upload page
     */
    public function qrStep(): View
    {
        return view('products.qr.qr-step', [
            'currentStep' => 2
        ]);
    }

    /**
     * STEP 2: Store QR code to session
     */
    public function storeQr(Request $request)
    {
        if ($request->hasFile('qr_code')) {
            // UPLOAD DIRECTLY TO FINAL FOLDER (Public)
            $qrPath = $request->file('qr_code')->store('qr_codes', [
                'disk' => 's3',
                'visibility' => 'public',
            ]);

            session(['product_qr' => $qrPath]);
        }

        return redirect()->route('sell-item.final-step')
            ->with('success', 'QR uploaded! Review and finalize.');
    }

    /**
     * STEP 2: Skip QR upload
     */
    public function skipQr(): RedirectResponse
    {
        session()->forget('product_qr');

        return redirect()->route('sell-item.final-step')
            ->with('info', 'QR skipped. Review your product.');
    }

    /**
     * STEP 3: Final review (read from session)
     */
    public function finalStep(): View
    {
        return view('products.qr.qr-final-step', [
            'step1'       => session('product_step1'),
            'images'      => session('product_images'),
            'qr'          => session('product_qr'),
            'currentStep' => 3
        ]);
    }

    /**
     * FINAL SUBMIT: Create product in DB and Upload to S3
     */
    public function finalize()
    {
        $step1  = session('product_step1');
        $images = session('product_images', []);
        $qr     = session('product_qr');

        if (!$step1) {
            return redirect()->route('products.create')
                ->withErrors('Session expired. Please start again.');
        }

        DB::beginTransaction();

        try {
            // 1. Assign QR path to product data BEFORE creating
            if ($qr) {
                $step1['qr_code'] = $qr;
            }

            // 2. Create Product
            $product = Product::create($step1);

            // 3. Move IMAGES (Keep this! Images are still in temp)
            foreach ($images as $tempPath) {
                if (Storage::disk('s3')->exists($tempPath)) {
                    $fileName = basename($tempPath);
                    $finalPath = 'products/' . $fileName;

                    Storage::disk('s3')->move($tempPath, $finalPath);
                    Storage::disk('s3')->setVisibility($finalPath, 'public');

                    $product->images()->create(['image' => $finalPath]);
                }
            }

            DB::commit();
            session()->forget(['product_step1', 'product_images', 'product_qr']);

            return redirect()->route('products.show', $product->id)
                ->with('success', 'Product published!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors('Error: ' . $e->getMessage());
        }
    }

    public function index(): View
    {
        $allProducts = $this->productService->getProductsByUser(Auth::id());
        // 2. Filter the collection into groups
        $approved = $allProducts->where('approval_status', 'approved');
        $pending  = $allProducts->where('approval_status', 'pending');
        $rejected = $allProducts->where('approval_status', 'rejected');

        // 3. Pass the separated lists to the view
        return view('products.index', [
            'approved' => $approved,
            'pending'  => $pending,
            'rejected' => $rejected
        ]);
        return view('products.index', compact('products'));
    }

    public function create(): View
    {
        $categories = $this->categoryService->getAllCategories();
        $segments = Segment::all();
        $barangays = Barangay::all();

        return view('products.create', [
            'categories' => $categories,
            'segments' => $segments,
            'barangays' => $barangays,
            'currentStep' => 1,
        ]);
    }

    public function edit(Product $product): View
    {
        $categories = $this->categoryService->getAllCategories();
        $segments = Segment::all();
        $barangays = Barangay::all();

        return view('products.edit', compact('product', 'categories', 'segments', 'barangays'));
    }

    public function update(UpdateProductRequest $request, Product $product)
    {
        // 1️⃣ Validate request
        $validated = $request->validated();

        // If the item was rejected, editing it resets status to 'pending' for review.
        if ($product->approval_status === 'rejected') {
            $validated['approval_status'] = 'pending';
        }
        // --------------------------------------------

        // 1a️⃣ Handle QR code only for verified users
        if ($request->user()?->is_verified && $request->hasFile('qr_code')) {
            // Delete old QR from S3 if exists
            if ($product->qr_code && Storage::disk('s3')->exists($product->qr_code)) {
                Storage::disk('s3')->delete($product->qr_code);
            }
            // Upload new QR
            $validated['qr_code'] = $request->file('qr_code')->store('qr_codes', [
                'disk' => 's3',
                'visibility' => 'public',
            ]);
        } else {
            // Prevent users from tampering with QR code field if they didn't upload one
            unset($validated['qr_code']);
        }

        // 2️⃣ Prepare images array for service
        $images = [
            'main' => $request->file('image'),       // Main product image (if you have one)
            'gallery' => $request->file('images', []) // Gallery images
        ];

        // 3️⃣ Call service to handle update including S3 uploads
        $this->productService->updateProduct($product, $validated, $images);

        // 4️⃣ Handle deletion of gallery images (User clicked 'X')
        $deleteIds = collect($request->input('deleted_images', []))
            ->map(fn($id) => (int)$id)
            ->filter()
            ->unique()
            ->values()
            ->all();

        if (!empty($deleteIds)) {
            $imagesToDelete = $product->images()->whereIn('id', $deleteIds)->get(['id', 'image']);
            foreach ($imagesToDelete as $img) {
                if ($img->image && Storage::disk('s3')->exists($img->image)) {
                    Storage::disk('s3')->delete($img->image);
                }
            }
            Image::where('product_id', $product->id)->whereIn('id', $deleteIds)->delete();
        }

        // 5️⃣ Redirect with appropriate message
        $message = ($product->approval_status === 'rejected')
            ? 'Product updated and resubmitted for approval!'
            : 'Product updated successfully!';

        return redirect()->route('products.show', $product)
            ->with('success', $message);
    }

    public function show($id)
    {
        Cache::forget("product_{$id}_comments");
        Cache::forget("product_{$id}_with_comments");

        $product = Product::with(['user', 'category', 'images'])->findOrFail($id);

        // Load comments logic...
        $allComments = \App\Models\Comment::with(['user'])
            ->where('product_id', $id)
            ->orderBy('created_at', 'asc')
            ->get();

        $byParent = $allComments->groupBy('parent_id');
        $topLevel = $byParent->get(null, collect())->sortByDesc('created_at')->values();

        $topLevel->each(function ($root) use ($byParent) {
            $flatReplies = collect();
            $stack = [];
            foreach ($byParent->get($root->id, collect()) as $child) {
                $flatReplies->push($child);
                $stack[] = $child;
            }
            while (!empty($stack)) {
                $node = array_pop($stack);
                foreach ($byParent->get($node->id, collect()) as $child) {
                    $flatReplies->push($child);
                    $stack[] = $child;
                }
            }
            $root->setRelation('replies', $flatReplies);
        });

        $product->setRelation('comments', $topLevel);
        $moreProducts = $this->productService->getMoreProductsByUser($product->user_id, $product->id);

        // Check if there's any active order with payment proof (not cancelled)
        $hasActiveOrder = Order::where('product_id', $product->id)
            ->whereNotNull('proof')
            ->where('status', '!=', 'cancelled')
            ->exists();

        return response()->view('products.show', compact('product', 'moreProducts', 'hasActiveOrder'))
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0')
            ->header('Last-Modified', gmdate('D, d M Y H:i:s') . ' GMT')
            ->header('ETag', md5(serialize($product->comments)));
    }

    public function destroy(Product $product): RedirectResponse
    {
        $this->productService->deleteProduct($product);
        return redirect()->route('products.index');
    }

    public function markAsSold(Product $product): RedirectResponse
    {
        if (!Auth::check() || Auth::id() !== $product->user_id) {
            abort(403);
        }

        $product->update(['status' => 'sold']);

        return redirect()->route('products.show', $product)
            ->with('success', 'Item marked as sold.');
    }
}
