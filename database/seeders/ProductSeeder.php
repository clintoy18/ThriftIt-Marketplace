<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\User;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $categories = [
            1 => 'Shirts',
            2 => 'Pants',
            3 => 'Dresses',
            4 => 'Shoes',
            5 => 'Accessories',
            6 => 'Outerwear',
            7 => 'Shorts',
            8 => 'Skirts',
            9 => 'Hats',
            10 => 'Socks',
            11 => 'Jackets',
            12 => 'Polo',
            13 => 'Others'
        ];
 $products = [
            ['name'=>'Retro Denim Jacket','category_id'=>11],
            ['name'=>'Floral Summer Dress','category_id'=>3],
            ['name'=>'Vintage Cargo Pants','category_id'=>2],
            ['name'=>'Black Oversized Hoodie','category_id'=>6],
            ['name'=>'Knit Sweater - Cream','category_id'=>1],
            ['name'=>'Classic Polo Shirt','category_id'=>12],
            ['name'=>'Slim Fit Jeans','category_id'=>2],
            ['name'=>'Corduroy Button-Down Shirt','category_id'=>1],
            ['name'=>'Beige Pleated Skirt','category_id'=>8],
            ['name'=>'Graphic Tee - Urban Edition','category_id'=>1],
            ['name'=>'Leather Belt','category_id'=>5],
            ['name'=>'Canvas Sneakers','category_id'=>4],
            ['name'=>'Wool Scarf','category_id'=>5],
            ['name'=>'Denim Shorts','category_id'=>7],
            ['name'=>'Hooded Raincoat','category_id'=>11],
            ['name'=>'Silk Blouse','category_id'=>1],
            ['name'=>'Plaid Shirt','category_id'=>1],
            ['name'=>'Corduroy Pants','category_id'=>2],
            ['name'=>'Trench Coat','category_id'=>11],
            ['name'=>'Striped Tee','category_id'=>1],
            ['name'=>'Leather Boots','category_id'=>4],
            ['name'=>'Mini Backpack','category_id'=>5],
            ['name'=>'Bucket Hat','category_id'=>9],
            ['name'=>'Sports Jacket','category_id'=>11],
            ['name'=>'Chinos Pants','category_id'=>2],
            ['name'=>'Maxi Dress','category_id'=>3],
            ['name'=>'Puffer Vest','category_id'=>6],
            ['name'=>'Cardigan Sweater','category_id'=>1],
            ['name'=>'Ripped Jeans','category_id'=>2],
            ['name'=>'Vintage Hoodie','category_id'=>6],
            ['name'=>'Suede Jacket','category_id'=>11],
            ['name'=>'Tie-Dye Shirt','category_id'=>1],
            ['name'=>'Wool Coat','category_id'=>11],
            ['name'=>'Leather Gloves','category_id'=>5],
            ['name'=>'Denim Overalls','category_id'=>2],
            ['name'=>'Pleated Pants','category_id'=>2],
            ['name'=>'Long Cardigan','category_id'=>1],
            ['name'=>'Denim Vest','category_id'=>6],
            ['name'=>'Printed Blouse','category_id'=>1],
            ['name'=>'Bomber Jacket','category_id'=>11],
            ['name'=>'High-Waist Shorts','category_id'=>7],
            ['name'=>'Oversized Tee','category_id'=>1],
            ['name'=>'Leather Skirt','category_id'=>8],
            ['name'=>'Fleece Jacket','category_id'=>6],
            ['name'=>'Checked Blazer','category_id'=>11],
            ['name'=>'Cropped Hoodie','category_id'=>6],
            ['name'=>'Sweatpants','category_id'=>2],
            ['name'=>'Linen Shirt','category_id'=>1],
            ['name'=>'Corduroy Skirt','category_id'=>8],
            ['name'=>'Vintage Sneakers','category_id'=>4],
            ['name'=>'Flannel Shirt','category_id'=>1],
            ['name'=>'Hooded Sweatshirt','category_id'=>6],
            ['name'=>'Silk Scarf','category_id'=>5],
            ['name'=>'Vintage Jeans','category_id'=>2],
            ['name'=>'Chunky Knit Sweater','category_id'=>1],
            ['name'=>'Baseball Cap','category_id'=>9],
            ['name'=>'Denim Shirt','category_id'=>1],
            ['name'=>'Polo Dress','category_id'=>3],
            ['name'=>'Leather Sandals','category_id'=>4],
            ['name'=>'Sports Shorts','category_id'=>7],
            ['name'=>'Cargo Jacket','category_id'=>11],
            ['name'=>'Wool Pants','category_id'=>2],
            ['name'=>'Denim Romper','category_id'=>3],
            ['name'=>'Graphic Hoodie','category_id'=>6],
            ['name'=>'Knit Beanie','category_id'=>9],
        ];

           $descriptions = [
            'Lightly used and in great condition.',
            'Minimal signs of wear. Very comfortable.',
            'Perfect for everyday outfits.',
            'Soft fabric, stylish, and clean.',
            'Trendy piece, still looks new.',
        ];

        $sizes = ['XS','S','M','L','XL','XXL'];

        // Get all regular users
        $regularUsers = User::where('role','0')->get();

        // Product counts per user
        $userProductCounts = [10,8,4,19,20];
        $currentIndex = 0;

        foreach($regularUsers as $key => $user){
            $count = $userProductCounts[$key] ?? 0;

            for($i=0; $i<$count; $i++){
                if(!isset($products[$currentIndex])) break;

                Product::create([
                    'user_id' => $user->id,
                    'category_id' => $products[$currentIndex]['category_id'],
                    'name' => $products[$currentIndex]['name'],
                    'description' => $descriptions[array_rand($descriptions)],
                    'price' => rand(80,350),
                    'approval_status' => 'approved',
                    'size' => $sizes[array_rand($sizes)],
                    'image' => 'https://via.placeholder.com/300x300.png?text='.urlencode($products[$currentIndex]['name']),
                    'qty' => rand(1,10),
                    'status' => 'available',
                    'segment_id' => 1,
                    'barangay_id' => 1,
                    'qr_code' => 'QR'.rand(1000,9999),
                    'admin_notes' => 'Seeded product',
                ]);

                $currentIndex++;
            }
        }
    }
}
