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
        Schema::create('price_offers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->foreignId('buyer_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('seller_id')->constrained('users')->onDelete('cascade');
            $table->decimal('quantity', 10, 2);
            $table->decimal('offered_price', 12, 2);
            $table->decimal('original_price', 12, 2);
            $table->decimal('counter_price', 12, 2)->nullable();
            $table->string('shipping_method')->default('Ambil di Lokasi Petani');
            $table->text('notes')->nullable();
            $table->enum('status', [
                'Menunggu',
                'Diterima',
                'Ditolak',
                'Counter Offer',
                'Selesai',
                'Dibatalkan'
            ])->default('Menunggu');
            $table->timestamps();

            $table->index(['buyer_id', 'status']);
            $table->index(['seller_id', 'status']);
            $table->index(['product_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('price_offers');
    }
};
