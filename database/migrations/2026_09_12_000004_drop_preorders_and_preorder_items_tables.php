<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Drop preorder_items first because of foreign key reference to preorders
        Schema::dropIfExists('preorder_items');

        // 2. Drop preorders table
        Schema::dropIfExists('preorders');

        // 3. Update orders source_type enum to remove 'preorder'
        // First sanitize any existing records if needed
        DB::table('orders')->where('source_type', 'preorder')->update(['source_type' => 'direct_purchase']);

        // Alter enum column in MySQL
        DB::statement("ALTER TABLE `orders` MODIFY COLUMN `source_type` ENUM('direct_purchase', 'negotiation', 'commodity_request') NOT NULL DEFAULT 'direct_purchase'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Re-allow 'preorder' in orders source_type
        DB::statement("ALTER TABLE `orders` MODIFY COLUMN `source_type` ENUM('direct_purchase', 'negotiation', 'commodity_request', 'preorder') NOT NULL DEFAULT 'direct_purchase'");

        Schema::create('preorders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('commodity_id')->constrained('commodities')->onDelete('cascade');
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->decimal('price', 12, 2);
            $table->decimal('estimated_production', 10, 2);
            $table->decimal('preorder_available_quantity', 10, 2);
            $table->decimal('min_order', 10, 2)->default(1);
            $table->string('unit')->default('kg');
            $table->date('estimated_harvest_date');
            $table->string('location');
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->string('image')->nullable();
            $table->enum('status', [
                'Dibuka',
                'Menunggu Panen',
                'Siap Diproses',
                'Diproses',
                'Selesai',
                'Dibatalkan'
            ])->default('Dibuka');
            $table->timestamps();

            $table->index(['commodity_id', 'status']);
            $table->index(['user_id', 'status']);
        });

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
};
