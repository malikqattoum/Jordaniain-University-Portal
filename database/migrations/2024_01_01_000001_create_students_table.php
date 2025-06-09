<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('student_id')->unique();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('passport_number')->nullable();
            $table->string('college');
            $table->string('major');
            $table->string('academic_year');
            $table->decimal('total_credit_hours', 5, 1)->default(0);
            $table->decimal('cumulative_gpa', 3, 2)->default(0);
            $table->decimal('successful_credit_hours', 5, 1)->default(0);
            $table->enum('status', ['active', 'inactive', 'graduated', 'suspended'])->default('active');
            $table->rememberToken();
            $table->timestamps();
        });
        // Set table charset and collation to utf8mb4
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE students CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    }

    public function down()
    {
        Schema::dropIfExists('students');
    }
};
