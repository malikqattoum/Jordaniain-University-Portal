<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSemesterFeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('semester_fees', function (Blueprint $table) {
            $table->id();
            $table->string('semester_name'); // e.g., "Fall 2024", "Spring 2025"
            $table->decimal('semester_fees', 8, 2); // Total semester fees amount
            $table->string('academic_year'); // e.g., "2024/2025"
            $table->boolean('is_active')->default(false); // Current active semester
            $table->text('description')->nullable();
            $table->timestamps();

            $table->index('academic_year');
            $table->index('is_active');
            $table->index('semester_name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('semester_fees');
    }
}
