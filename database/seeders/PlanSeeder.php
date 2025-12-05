<?php

namespace Database\Seeders;
use App\Models\Plan;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $plans = [
            [
                'name' => 'Starter Rack',
                'stripe_plan_id' => 'prod_TXeLIH6C3eBbuY',
                'stripe_price_id' => 'price_1SaZ39L7IAkQRknTexXoaHRB',
            ],
            [
                'name' => 'Bargain Shelf',
                'stripe_plan_id' => 'prod_TXeK42zx4mikcP',
                'stripe_price_id' => 'price_1SaZ1ZL7IAkQRknTb8DrpVdY',
            ],
            [
                'name' => 'Vintage Vault',
                'stripe_plan_id' => 'prod_TXeIKafMmVOk7B',
                'stripe_price_id' => 'price_1SaYzoL7IAkQRknTvMcfoH07',
            ],
        ];

        foreach ($plans as $plan) {
          Plan::create($plan);
        }
    }
}
