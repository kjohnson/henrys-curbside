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
        Schema::table('availability_checks', function (Blueprint $table) {
            // Filled in by geocoding the address, for the admin map. Null until (or unless)
            // the address could be matched.
            $table->decimal('latitude', 10, 7)->nullable()->after('postal_code');
            $table->decimal('longitude', 10, 7)->nullable()->after('latitude');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('availability_checks', function (Blueprint $table) {
            $table->dropColumn(['latitude', 'longitude']);
        });
    }
};
