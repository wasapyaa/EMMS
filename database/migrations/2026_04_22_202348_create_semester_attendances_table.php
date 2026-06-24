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
        Schema::create('semester_attendances', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('s_id');
            $table->string('semester_name');
            $table->string('event_name');
            $table->dateTime('event_date');
            $table->integer('merit_value');
            $table->timestamps();

            $table->foreign('s_id')->references('s_id')->on('students')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('semester_attendances');
    }
};
