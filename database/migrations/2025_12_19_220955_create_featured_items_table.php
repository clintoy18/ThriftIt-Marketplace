<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('featured_buyer_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('featured_buyer_id')->constrained()->onDelete('cascade');
            $table->string('product_name');
            $table->decimal('price', 10, 2);
            $table->string('size')->nullable();
            $table->string('category_label')->nullable();
            $table->string('image_path'); // S3 path for product photo
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('featured_items');
    }
};
