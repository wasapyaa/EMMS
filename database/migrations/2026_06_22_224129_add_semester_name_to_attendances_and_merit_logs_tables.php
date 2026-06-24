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
        Schema::table('attendances', function (Blueprint $table) {
            $table->string('semester_name')->default('current')->after('status');
        });

        Schema::table('merit_logs', function (Blueprint $table) {
            $table->string('semester_name')->default('current')->after('points_added');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropColumn('semester_name');
        });

        Schema::table('merit_logs', function (Blueprint $table) {
            $table->dropColumn('semester_name');
        });
    }
};
