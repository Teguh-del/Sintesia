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
        Schema::create('request_offers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('commodity_request_id')->constrained('commodity_requests')->onDelete('cascade');
            $table->foreignId('farmer_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('product_id')->nullable()->constrained('products')->nullOnDelete();
            $table->foreignId('stock_id')->nullable()->constrained('stocks')->nullOnDelete();
            $table->decimal('offered_quantity', 10, 2);
            $table->decimal('offered_price', 12, 2);
            $table->string('shipping_method')->default('Ambil di Lokasi Petani');
            $table->text('notes')->nullable();
            $table->enum('status', [
                'Menunggu',
                'Diterima',
                'Ditolak',
                'Dibatalkan'
            ])->default('Menunggu');
            $table->timestamps();

            $table->index(['commodity_request_id', 'status']);
            $table->index(['farmer_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('request_offers');
    }
};
