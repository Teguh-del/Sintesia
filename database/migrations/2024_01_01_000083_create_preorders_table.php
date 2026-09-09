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
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('preorders');
    }
};
