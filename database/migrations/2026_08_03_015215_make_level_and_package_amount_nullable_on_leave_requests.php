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
        Schema::table('leave_requests', function (Blueprint $table) {
            $table->unsignedTinyInteger('level')->nullable()->default(null)->change();
            $table->unsignedInteger('package_amount')->nullable()->default(null)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leave_requests', function (Blueprint $table) {
            $table->unsignedTinyInteger('level')->default(1)->change();
            $table->unsignedInteger('package_amount')->default(8000)->change();
        });
    }
};
