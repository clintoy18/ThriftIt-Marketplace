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
        Schema::create('featured_buyers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // The shop owner
            $table->string('name');
            $table->string('handle')->nullable();
            $table->string('follower_count')->nullable(); // e.g. "2.3M"
            $table->string('bio')->nullable(); // e.g. "Sustainable Fashion Creator"
            $table->text('testimonial')->nullable();
            $table->string('avatar_path')->nullable(); // S3 path for profile pic
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('featured_buyers');
    }
};
