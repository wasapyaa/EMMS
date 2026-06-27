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
        Schema::table('idea_votes', function (Blueprint $table) {
            $table->string('type', 10)->default('like')->after('s_id'); // 'like' or 'dislike'
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('idea_votes', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }
};
