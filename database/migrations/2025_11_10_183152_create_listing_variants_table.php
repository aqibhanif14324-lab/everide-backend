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
        Schema::create('listing_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('listing_id')->constrained('listings')->onDelete('cascade');
            $table->string('sku')->unique();
            $table->decimal('price', 10, 2)->nullable();
            $table->decimal('compare_at_price', 10, 2)->nullable();
            $table->decimal('cost_price', 10, 2)->nullable();
            $table->integer('stock_qty')->default(0);
            $table->boolean('is_default')->default(false);
            $table->integer('weight_grams')->nullable();
            $table->json('dimensions')->nullable();
            $table->string('barcode')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('listing_variants');
    }
};
