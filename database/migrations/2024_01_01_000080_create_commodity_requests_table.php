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
        Schema::create('commodity_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('commodity_id')->constrained('commodities')->onDelete('cascade');
            $table->string('title');
            $table->decimal('required_quantity', 10, 2);
            $table->string('unit')->default('kg');
            $table->decimal('max_price', 12, 2);
            $table->string('location');
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->date('deadline');
            $table->text('description')->nullable();
            $table->enum('status', [
                'Aktif',
                'Mendapat Penawaran',
                'Dipenuhi',
                'Ditutup',
                'Dibatalkan'
            ])->default('Aktif');
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
        Schema::dropIfExists('commodity_requests');
    }
};
