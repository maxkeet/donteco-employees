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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('full_or_part_time', ['F', 'P'])->nullable();
            $table->unsignedBigInteger('job_title_id');
            $table->unsignedBigInteger('salary_info_id');
            $table->unique(['name', 'job_title_id', 'salary_info_id'], 'unique_name_job_title_id_salary_info_id');
            $table->foreign('job_title_id')->references('id')->on('job_titles')->onDelete('cascade');
            $table->foreign('salary_info_id')->references('id')->on('salary_infos')->onDelete('cascade');
            $table->timestamps();
        });

        Schema::table('employees', function (Blueprint $table) {
            $table->index('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
