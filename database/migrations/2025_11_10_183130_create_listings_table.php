<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('listings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shop_id')->constrained('shops')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');

            $table->string('title');
            $table->string('slug', 191)->unique(); // safer for older MySQL with utf8mb4
            $table->longText('description')->nullable();

            $table->foreignId('category_id')->nullable()->constrained('categories')->onDelete('set null');

            // shrinked lengths to keep composite index under key-length limit
            $table->string('brand', 100)->nullable();
            $table->string('model', 100)->nullable();

            $table->integer('year')->nullable();

            $table->enum('condition', ['new', 'very_good', 'good', 'for_parts'])->default('good');
            $table->enum('status', ['draft', 'published', 'reserved', 'sold', 'archived'])->default('draft');
            $table->timestamp('published_at')->nullable();

            $table->decimal('default_price', 10, 2);
            $table->string('currency', 3)->default('EUR');

            $table->string('location_city')->nullable();
            $table->string('location_country')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index('category_id');
            $table->index('status');
            $table->index('published_at');

            // composite index — now safe with 100/100 lengths
            $table->index(['brand', 'model', 'year'], 'listings_brand_model_year_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('listings');
    }
};
