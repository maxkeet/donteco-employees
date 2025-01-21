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
        Schema::create('salary_infos', function (Blueprint $table) {
            $table->id();
            $table->enum('salary_or_hourly', ['SALARY', 'HOURLY']);
            $table->integer('typical_hours')->nullable();
            $table->float('annual_salary')->nullable();
            $table->float('hourly_rate')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('salary_infos');
    }
};
