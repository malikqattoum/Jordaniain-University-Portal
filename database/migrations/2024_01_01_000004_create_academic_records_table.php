<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('academic_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->string('academic_year');
            $table->integer('semester');
            $table->decimal('semester_credit_hours', 5, 1)->default(0);
            $table->decimal('semester_gpa', 3, 2)->default(0);
            $table->decimal('cumulative_credit_hours', 5, 1)->default(0);
            $table->decimal('cumulative_gpa', 3, 2)->default(0);
            $table->decimal('successful_credit_hours', 5, 1)->default(0);
            $table->enum('semester_status', ['regular', 'probation', 'warning', 'excellent', 'honor'])->default('regular');
            $table->timestamps();

            $table->unique(['student_id', 'academic_year', 'semester']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('academic_records');
    }
};