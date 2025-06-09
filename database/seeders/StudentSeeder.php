<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Student;

class StudentSeeder extends Seeder
{
    public function run()
    {
        Student::create([
            'student_id' => '20210001',
            'name' => 'جميلة العبدالله',
            'name_en' => 'JAMILA AL-ABDULLAH',
            'username' => 'jml20210001',
            'email' => 'jamila.abdullah@ju.edu.jo',
            'password' => Hash::make('password123'),
            'passport_number' => 'N123456789',
            'college' => 'كلية الحقوق',
            'major' => 'القانون',
            'academic_year' => '2021/2022',
            'total_credit_hours' => 24.0,
            'cumulative_gpa' => 3.59,
            'successful_credit_hours' => 33.0,
            'status' => 'active'
        ]);

        Student::create([
            'student_id' => '20210002',
            'name' => 'أحمد محمد الأحمد',
            'name_en' => 'AHMED MOHAMMED AL-AHMED',
            'username' => 'ahd20210002',
            'email' => 'ahmed.ahmad@ju.edu.jo',
            'password' => Hash::make('password123'),
            'passport_number' => 'N987654321',
            'college' => 'كلية الحقوق',
            'major' => 'القانون',
            'academic_year' => '2021/2022',
            'total_credit_hours' => 30.0,
            'cumulative_gpa' => 3.25,
            'successful_credit_hours' => 45.0,
            'status' => 'active'
        ]);

        Student::create([
            'student_id' => '20210003',
            'name' => 'فاطمة علي السالم',
            'name_en' => 'FATIMA ALI AL-SALEM',
            'username' => 'ftm20210003',
            'email' => 'fatima.salem@ju.edu.jo',
            'password' => Hash::make('password123'),
            'passport_number' => 'N456789123',
            'college' => 'كلية الحقوق',
            'major' => 'القانون',
            'academic_year' => '2021/2022',
            'total_credit_hours' => 27.0,
            'cumulative_gpa' => 3.75,
            'successful_credit_hours' => 39.0,
            'status' => 'active'
        ]);
    }
}