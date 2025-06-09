<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('students', function (Blueprint $table) {
            $table->string('name_en')->nullable()->after('name')->charset('utf8mb4')->collation('utf8mb4_unicode_ci');
            $table->string('username')->unique()->nullable()->after('student_id')->charset('utf8mb4')->collation('utf8mb4_unicode_ci');
        });
    }

    public function down()
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn(['name_en', 'username']);
        });
    }
};
