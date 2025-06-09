<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            StudentSeeder::class,
            CourseSeeder::class,
            EnrollmentSeeder::class,
            AcademicRecordSeeder::class,
            EquivalentCourseSeeder::class,
            NewStudentsSeeder::class,
            KhalidStudentSeeder::class,
        ]);
    }
}
