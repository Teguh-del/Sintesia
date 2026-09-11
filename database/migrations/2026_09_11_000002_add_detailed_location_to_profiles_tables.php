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
        Schema::table('farmer_profiles', function (Blueprint $table) {
            $table->string('province', 100)->nullable()->after('primary_commodity');
            $table->string('city', 100)->nullable()->after('province');
            $table->string('district', 100)->nullable()->after('city');
            $table->string('village', 100)->nullable()->after('district');
        });

        Schema::table('collector_profiles', function (Blueprint $table) {
            $table->string('province', 100)->nullable()->after('business_type');
            $table->string('city', 100)->nullable()->after('province');
            $table->string('district', 100)->nullable()->after('city');
            $table->string('village', 100)->nullable()->after('district');
        });

        Schema::table('consumer_profiles', function (Blueprint $table) {
            $table->string('province', 100)->nullable()->after('user_id');
            $table->string('city', 100)->nullable()->after('province');
            $table->string('district', 100)->nullable()->after('city');
            $table->string('village', 100)->nullable()->after('district');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('farmer_profiles', function (Blueprint $table) {
            $table->dropColumn(['province', 'city', 'district', 'village']);
        });

        Schema::table('collector_profiles', function (Blueprint $table) {
            $table->dropColumn(['province', 'city', 'district', 'village']);
        });

        Schema::table('consumer_profiles', function (Blueprint $table) {
            $table->dropColumn(['province', 'city', 'district', 'village']);
        });
    }
};
