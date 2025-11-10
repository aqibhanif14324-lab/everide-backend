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
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            $table->foreignId('listing_id')->constrained('listings')->onDelete('cascade');
            $table->foreignId('variant_id')->nullable()->constrained('listing_variants')->onDelete('set null');
            $table->string('title_snapshot');
            $table->string('sku_snapshot');
            $table->decimal('unit_price', 10, 2);
            $table->integer('quantity');
            $table->decimal('line_total', 10, 2);
            $table->json('selected_attributes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
