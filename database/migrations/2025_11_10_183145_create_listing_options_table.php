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
        Schema::create('listing_options', function (Blueprint $table) {
            $table->foreignId('listing_id')->constrained('listings')->onDelete('cascade');
            $table->foreignId('option_id')->constrained('options')->onDelete('cascade');
            $table->primary(['listing_id', 'option_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('listing_options');
    }
};
