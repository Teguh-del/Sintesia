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
        Schema::create('stocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('commodity_id')->constrained('commodities')->cascadeOnDelete();
            $table->foreignId('harvest_id')->nullable()->constrained('harvests')->nullOnDelete();
            $table->string('batch_code')->unique();
            $table->decimal('initial_quantity', 10, 2);
            $table->decimal('available_quantity', 10, 2)->default(0);
            $table->decimal('ordered_quantity', 10, 2)->default(0);
            $table->decimal('sold_quantity', 10, 2)->default(0);
            $table->string('unit', 50)->default('kg');
            $table->string('quality', 100)->default('Grade B (Standar)');
            $table->enum('status', ['Tersedia', 'Stok Terbatas', 'Habis'])->default('Tersedia');
            $table->timestamps();

            $table->index(['user_id', 'commodity_id']);
            $table->index('status');
            $table->index('available_quantity');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stocks');
    }
};
