<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Work;

class WorkSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Work::create([
            'user_id' => 2,
            'title' => 'Upcycled Denim Jacket',
            'description' => 'A stylish denim jacket made from repurposed materials.',
            'image' => 'works_images/denim_jacket.jpg',
            'status' => 'completed',
        ]);
    }
}
