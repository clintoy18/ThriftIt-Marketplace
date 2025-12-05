<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Product;
use App\Models\Categories;
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

class ProductController extends Controller
{
    protected $productService, $categoryService;

    public function __construct(ProductService $productService, CategoriesService $categoryService)
    {
        $this->productService = $productService;
        $this->categoryService = $categoryService;
        $this->middleware('subscribed')->only(['create', 'store']);
        //us middleware to ensure seller is verified before accessing qr upload routes
        $this->middleware('verified.seller')->only(['qrStep', 'storeQr']);
    }

    public function index(): View
    {
        $products = $this->productService->getProductsByUser(Auth::id());
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
            'currentStep' => 1, // <-- pass current step
        ]);
    }

    public function store(StoreProductRequest $request)
    {
        $validated = $request->validated();
        $validated['user_id'] = Auth::id();

        $images = $request->file('images', []);
        $product = $this->productService->createProduct($validated, $images);

        // Check if user is verified
        if (!Auth::user()->is_verified) {
            return redirect()
                ->route('products.index', $product->id)
                ->with('success', 'Product created!');
        }

        // Verified users can upload a QR code (Step 2)
        return redirect()
            ->route('sell-item.qr', $product->id)
            ->with('success', 'Product created! You can now upload a QR code.');
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

        // 2️⃣ Prepare images array for service
        $images = [
            'main' => $request->file('image'),       // Main product image
            'gallery' => $request->file('images', []) // Gallery images
        ];

        // 3️⃣ Call service to handle update including S3 uploads
        $this->productService->updateProduct($product, $validated, $images);

        // 4️⃣ Handle deletion of gallery images if any
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

        // 5️⃣ Redirect with success message
        return redirect()->route('products.show', $product)
            ->with('success', 'Product updated successfully!');
    }



    public function show($id)
    {
        Cache::forget("product_{$id}_comments");
        Cache::forget("product_{$id}_with_comments");

        $product = Product::with(['user', 'category', 'images'])->findOrFail($id);
        // Load ALL comments for this product and build an unlimited-depth flattened replies list per top-level
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
                /** @var \App\Models\Comment $node */
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

        return response()->view('products.show', compact('product', 'moreProducts'))
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
        // only owner may mark as sold
        if (!Auth::check() || Auth::id() !== $product->user_id) {
            abort(403);
        }

        // update status
        $product->update(['status' => 'sold']);

        return redirect()->route('products.show', $product)
            ->with('success', 'Item marked as sold.');
    }

    // ✅ Step 2: Show optional QR upload page
    public function qrStep(Product $product): View
    {
        return view('products.qr.qr-step', [
            'product' => $product,
            'currentStep' => 2, // <-- pass current step
        ]);
    }

    // ✅ Step 2: Store QR if uploaded
    public function storeQr(Request $request, Product $product): RedirectResponse
    {
        if ($request->hasFile('qr_code')) {

            // Delete the old QR code from S3 if it exists
            if ($product->qr_code && Storage::disk('s3')->exists($product->qr_code)) {
                Storage::disk('s3')->delete($product->qr_code);
            }

            // Upload the new QR code to S3 with public visibility
            $path = $request->file('qr_code')->storePublicly('qr_codes', 's3');

            // Save the S3 file path to the product
            $product->qr_code = $path;
            $product->save();
        }

        // Redirect to final review page with a success message
        return redirect()->route('sell-item.final', $product->id)
            ->with('success', 'QR code uploaded! Review and finalize your product.');
    }


    // ✅ Step 2: Skip QR upload
    public function skipQr(Product $product): RedirectResponse
    {
        return redirect()->route('sell-item.final', $product->id)
            ->with('info', 'You chose to skip the QR code. Review and finalize your product.');
    }

    // Step 3: Finalize
    public function finalStep(Product $product): View
    {
        return view('products.qr.qr-final-step', [
            'product' => $product,
            'currentStep' => 3, // <-- pass current step
        ]);
    }
    // ✅ Step 3: Finalize product
    public function finalize(Product $product): RedirectResponse
    {
        return redirect()->route('products.show', $product->id)
            ->with('success', 'Item Listed successfully! Wait for approval.');
    }
}
