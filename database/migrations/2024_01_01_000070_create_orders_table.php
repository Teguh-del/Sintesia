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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->foreignId('buyer_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('seller_id')->constrained('users')->onDelete('cascade');
            $table->enum('source_type', ['direct_purchase', 'negotiation', 'commodity_request', 'preorder'])->default('direct_purchase');
            $table->enum('status', [
                'Menunggu Konfirmasi',
                'Dikonfirmasi',
                'Diproses',
                'Selesai',
                'Dibatalkan'
            ])->default('Menunggu Konfirmasi');
            $table->decimal('total_amount', 12, 2);
            $table->text('shipping_address');
            $table->string('shipping_method')->default('Ambil di Lokasi Petani');
            $table->decimal('shipping_cost', 10, 2)->default(0);
            $table->string('payment_method')->default('Transfer Bank / Rekber SINTESA');
            $table->enum('payment_status', ['Belum Dibayar', 'Sudah Dibayar', 'Dibatalkan'])->default('Belum Dibayar');
            $table->text('notes')->nullable();
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->text('cancellation_reason')->nullable();
            $table->timestamps();

            // Indexes for fast lookup
            $table->index(['buyer_id', 'status']);
            $table->index(['seller_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
