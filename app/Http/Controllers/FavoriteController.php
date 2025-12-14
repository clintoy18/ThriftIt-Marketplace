<?php

namespace App\Http\Controllers;

use App\Models\Favorite;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    /**
     * Display a listing of the user's favorite products.
     */
    public function index()
    {
        $user = Auth::user();
        $favorites = Favorite::where('user_id', $user->id)
            ->with(['product.images', 'product.category', 'product.barangay'])
            ->latest()
            ->paginate(20);

        $favoriteProductIds = $favorites->pluck('product_id')->toArray();

        return view('favorites.index', [
            'favorites' => $favorites,
            'favoriteProductIds' => $favoriteProductIds,
        ]);
    }

    /**
     * Toggle favorite status for a product.
     */
    public function toggle(Request $request, Product $product)
    {
        $user = Auth::user();

        $favorite = Favorite::where('user_id', $user->id)
            ->where('product_id', $product->id)
            ->first();

        if ($favorite) {
            // Unfavorite
            $favorite->delete();
            $isFavorited = false;
        } else {
            // Favorite
            Favorite::create([
                'user_id' => $user->id,
                'product_id' => $product->id,
            ]);
            $isFavorited = true;
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'isFavorited' => $isFavorited,
            ]);
        }

        return back();
    }

    /**
     * Check if a product is favorited by the current user.
     */
    public function check(Product $product)
    {
        $user = Auth::user();
        $isFavorited = Favorite::where('user_id', $user->id)
            ->where('product_id', $product->id)
            ->exists();

        return response()->json([
            'isFavorited' => $isFavorited,
        ]);
    }
}

