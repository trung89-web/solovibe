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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->restrictOnDelete();
            $table->string('name');
            $table->string('slug', 280)->unique();
            $table->string('sku', 64)->unique();
            $table->string('short_description', 500)->nullable();
            $table->longText('description')->nullable();
            $table->longText('care_instructions')->nullable();
            $table->enum('planting_season', ['spring', 'summer', 'autumn', 'winter', 'all_year'])->default('all_year');        
            $table->string('fruit_harvest_time', 150)->nullable();
            $table->string('tree_age', 50)->nullable();
            $table->unsignedSmallInteger('tree_height_cm')->nullable();
            $table->string('origin', 150)->nullable();
            $table->decimal('price', 12, 2);
            $table->decimal('sale_price', 12, 2)->nullable();
            $table->unsignedInteger('stock_quantity')->default(0);
            $table->unsignedInteger('sold_count')->default(0);
            $table->unsignedInteger('views_count')->default(0);
            $table->string('thumbnail')->nullable();
            $table->unsignedInteger('weight_gram')->nullable();
            $table->enum('status', ['draft', 'published', 'out_of_stock', 'archived'])->default('draft');
            $table->boolean('is_featured')->default(false);
            $table->string('meta_title')->nullable();
            $table->string('meta_description', 500)->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Đánh index để tối ưu truy vấn
            $table->index('planting_season');
            $table->index('status');
            $table->fullText(['name', 'description']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};