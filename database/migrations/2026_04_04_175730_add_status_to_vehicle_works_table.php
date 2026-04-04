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
        Schema::table('vehicle_works', function (Blueprint $table) {
            $table->string('status', 30)
                ->default('in_progress')
                ->after('job_photos');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vehicle_works', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
