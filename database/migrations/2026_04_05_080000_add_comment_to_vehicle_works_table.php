<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vehicle_works', function (Blueprint $table): void {
            $table->text('comment')
                ->nullable()
                ->after('job_description');
        });
    }

    public function down(): void
    {
        Schema::table('vehicle_works', function (Blueprint $table): void {
            $table->dropColumn('comment');
        });
    }
};
