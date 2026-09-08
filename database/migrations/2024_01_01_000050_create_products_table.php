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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('commodity_id')->constrained('commodities')->cascadeOnDelete();
            // stock_id will be connected in Phase 3 (Harvest & Stock)
            $table->unsignedBigInteger('stock_id')->nullable()->index();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->decimal('price', 12, 2); // Harga per satuan
            $table->decimal('stock', 10, 2)->default(0); // Stok tersedia
            $table->string('unit', 50)->default('kg');
            $table->decimal('min_order', 10, 2)->default(1);
            $table->string('quality', 100)->default('Standar'); // e.g. Grade A, Grade B, Organik
            $table->date('harvest_date')->nullable();
            $table->string('location');
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->enum('status', ['active', 'inactive', 'sold_out'])->default('active');
            $table->boolean('allow_negotiation')->default(true);
            $table->timestamps();

            // Indexes for fast searching, filtering, and sorting
            $table->index(['commodity_id', 'status']);
            $table->index(['price', 'status']);
            $table->index(['status', 'stock']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
