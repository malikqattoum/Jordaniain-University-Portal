<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEnrollmentDetailsToEnrollmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('enrollments', function (Blueprint $table) {
            $table->string('prerequisite')->nullable()->after('status');
            $table->string('nature')->nullable()->after('prerequisite');
            $table->string('teaching_method')->nullable()->after('nature');
            $table->string('course_index')->nullable()->after('teaching_method');
            $table->string('accounting_code')->nullable()->after('course_index');
            $table->string('section')->nullable()->after('accounting_code');
            $table->integer('section_number')->nullable()->after('section');
            $table->string('schedule_days')->nullable()->after('section_number');
            $table->string('schedule_time')->nullable()->after('schedule_days');
            $table->string('schedule_day')->nullable()->after('schedule_time');
            $table->boolean('is_in_person')->default(true)->after('schedule_day');
            $table->string('room')->nullable()->after('is_in_person');
            $table->string('instructor_name')->nullable()->after('room');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('enrollments', function (Blueprint $table) {
            $table->dropColumn([
                'prerequisite',
                'nature',
                'teaching_method',
                'course_index',
                'accounting_code',
                'section',
                'section_number',
                'schedule_days',
                'schedule_time',
                'schedule_day',
                'is_in_person',
                'room',
                'instructor_name'
            ]);
        });
    }
}
