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
        Schema::create('harvests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('commodity_id')->constrained('commodities')->cascadeOnDelete();
            $table->decimal('quantity', 10, 2);
            $table->string('unit', 50)->default('kg');
            $table->date('harvest_date');
            $table->string('quality', 100)->default('Grade B (Standar)');
            $table->string('location');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'commodity_id']);
            $table->index('harvest_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('harvests');
    }
};
