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
        Schema::table('sensor_data', function (Blueprint $table) {
            $table->foreignId('device_id')->nullable()->after('id')
                ->constrained('devices')->nullOnDelete();

            $table->json('readings')->nullable()->after('hujan_mm');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sensor_data', function (Blueprint $table) {
            $table->dropConstrainedForeignId('device_id');
            $table->dropColumn('readings');
        });
    }
};
