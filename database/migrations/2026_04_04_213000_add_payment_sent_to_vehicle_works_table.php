<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vehicle_works', function (Blueprint $table): void {
            $table->boolean('payment_sent')
                ->default(false)
                ->after('paid');
        });
    }

    public function down(): void
    {
        Schema::table('vehicle_works', function (Blueprint $table): void {
            $table->dropColumn('payment_sent');
        });
    }
};
