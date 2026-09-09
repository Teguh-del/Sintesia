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
        Schema::create('commodity_prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('commodity_id')->constrained('commodities')->cascadeOnDelete();
            $table->decimal('price', 12, 2);
            $table->string('unit', 50)->default('kg');
            $table->string('location', 150); // e.g. Pasar Induk Pare, Sleman, Kediri, Surabaya, Nasional
            $table->date('recorded_date');
            $table->string('source', 150)->default('Survei Pasar SINTESA'); // e.g. Pasar Tradisional, BPS, Pasar Induk
            $table->text('notes')->nullable();
            $table->timestamps();

            // Indexing for analytics query performance
            $table->index(['commodity_id', 'recorded_date']);
            $table->index(['location', 'recorded_date']);
            $table->index('recorded_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('commodity_prices');
    }
};
