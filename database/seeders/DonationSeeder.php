<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Donation;
use App\Models\User;

class DonationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Donation categories (make sure these IDs exist in DB)
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

        // Donation item list (name + category)
        $donations = [
            ['name'=>'Old Denim Jacket','category_id'=>11],
            ['name'=>'Used Summer Dress','category_id'=>3],
            ['name'=>'Pre-loved Cargo Pants','category_id'=>2],
            ['name'=>'Secondhand Hoodie','category_id'=>6],
            ['name'=>'Cotton Shirt','category_id'=>1],
            ['name'=>'Classic Polo','category_id'=>12],
            ['name'=>'Old Jeans','category_id'=>2],
            ['name'=>'Used Button-Down Shirt','category_id'=>1],
            ['name'=>'Pleated Skirt','category_id'=>8],
            ['name'=>'Printed Tee','category_id'=>1],
            ['name'=>'Used Belt','category_id'=>5],
            ['name'=>'Old Sneakers','category_id'=>4],
            ['name'=>'Wool Scarf','category_id'=>5],
            ['name'=>'Denim Shorts','category_id'=>7],
            ['name'=>'Rain Jacket','category_id'=>11],
            ['name'=>'Silk Blouse','category_id'=>1],
            ['name'=>'Plaid Shirt','category_id'=>1],
            ['name'=>'Corduroy Pants','category_id'=>2],
            ['name'=>'Trench Coat','category_id'=>11],
            ['name'=>'Striped Shirt','category_id'=>1],
            ['name'=>'Leather Boots','category_id'=>4],
            ['name'=>'Mini Backpack','category_id'=>5],
            ['name'=>'Bucket Hat','category_id'=>9],
            ['name'=>'Sports Jacket','category_id'=>11],
            ['name'=>'Chinos Pants','category_id'=>2],
            ['name'=>'Maxi Dress','category_id'=>3],
            ['name'=>'Puffer Vest','category_id'=>6],
            ['name'=>'Cardigan Sweater','category_id'=>1],
            ['name'=>'Ripped Jeans','category_id'=>2],
            ['name'=>'Old Hoodie','category_id'=>6],
            ['name'=>'Suede Jacket','category_id'=>11],
            ['name'=>'Tie-Dye Shirt','category_id'=>1],
            ['name'=>'Wool Coat','category_id'=>11],
            ['name'=>'Leather Gloves','category_id'=>5],
            ['name'=>'Denim Overalls','category_id'=>2],
            ['name'=>'Pleated Pants','category_id'=>2],
            ['name'=>'Long Cardigan','category_id'=>1],
            ['name'=>'Vest','category_id'=>6],
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

        // Random description list
        $descriptions = [
            'Still in good condition.',
            'Used but well-maintained.',
            'Light wear and tear only.',
            'Donated with care.',
            'Perfect for reuse.',
        ];

        // Sizes
        $sizes = ['XS','S','M','L','XL','XXL'];

        // Regular users only
        $regularUsers = User::where('role','0')->get();

        // Donation count distribution per user (like ProductSeeder)
        $donationCounts = [8, 10, 5, 12, 15];
        $currentIndex = 0;

        foreach ($regularUsers as $key => $user) {
            $count = $donationCounts[$key] ?? 0;

            for ($i = 0; $i < $count; $i++) {
                if (!isset($donations[$currentIndex])) break;

                Donation::create([
                    'user_id' => $user->id,
                    'category_id' => $donations[$currentIndex]['category_id'],
                    'name' => $donations[$currentIndex]['name'],
                    'description' => $descriptions[array_rand($descriptions)],
                    'size' => $sizes[array_rand($sizes)],
                    'approval_status' => 'approved',
                    // 'verification_status' => 'verified',
                    'status' => 'available',

                    // 'segment_id' => 1,
                    'barangay_id' => 1,

                    'image' => 'https://via.placeholder.com/300x300.png?text=' . urlencode($donations[$currentIndex]['name']),
                    'proof' => null,
                ]);

                $currentIndex++;
            }
        }
    }
}
