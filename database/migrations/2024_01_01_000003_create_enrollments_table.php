<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('enrollments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->string('academic_year');
            $table->integer('semester');
            $table->string('grade')->nullable();
            $table->decimal('grade_points', 3, 2)->nullable();
            $table->enum('status', ['enrolled', 'completed', 'withdrawn', 'incomplete'])->default('enrolled');
            $table->boolean('is_passed')->default(false);
            $table->timestamps();

            $table->unique(['student_id', 'course_id', 'academic_year', 'semester']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('enrollments');
    }
};