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
        Schema::create('preorder_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('preorder_id')->constrained('preorders')->onDelete('cascade');
            $table->foreignId('buyer_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('order_id')->nullable()->constrained('orders')->nullOnDelete();
            $table->decimal('quantity', 10, 2);
            $table->decimal('price_per_unit', 12, 2);
            $table->decimal('total_amount', 12, 2);
            $table->text('shipping_address');
            $table->string('shipping_method')->default('Ambil di Lokasi Petani');
            $table->text('notes')->nullable();
            $table->enum('status', [
                'Menunggu Panen',
                'Dikonfirmasi',
                'Selesai',
                'Dibatalkan'
            ])->default('Menunggu Panen');
            $table->timestamps();

            $table->index(['preorder_id', 'status']);
            $table->index(['buyer_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('preorder_items');
    }
};
