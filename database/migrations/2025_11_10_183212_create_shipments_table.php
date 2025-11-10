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
        Schema::create('shipments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            $table->enum('carrier', ['mondial_relay', 'relais_colis', 'shop2shop', 'colissimo', 'aggregator']);
            $table->string('tracking_number')->nullable();
            $table->string('pickup_id')->nullable();
            $table->string('pickup_name')->nullable();
            $table->string('pickup_address')->nullable();
            $table->string('pickup_city')->nullable();
            $table->string('pickup_postal_code')->nullable();
            $table->string('pickup_country')->nullable();
            $table->string('label_pdf_url')->nullable();
            $table->enum('status', ['created', 'labeled', 'in_transit', 'delivered', 'exception'])->default('created');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipments');
    }
};
