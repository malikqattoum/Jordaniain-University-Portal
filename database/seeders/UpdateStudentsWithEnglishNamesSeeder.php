<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Student;

class UpdateStudentsWithEnglishNamesSeeder extends Seeder
{
    public function run()
    {
        // Update existing students with English names and usernames
        $studentsData = [
            [
                'student_id' => '20210001',
                'name_en' => 'JAMILA AL-ABDULLAH',
                'username' => 'jml20210001'
            ],
            [
                'student_id' => '20210002',
                'name_en' => 'AHMED MOHAMMED AL-AHMED',
                'username' => 'ahd20210002'
            ],
            [
                'student_id' => '20210003',
                'name_en' => 'FATIMA ALI AL-SALEM',
                'username' => 'ftm20210003'
            ],
            [
                'student_id' => '20240001',
                'name_en' => 'IBRAHIM JASSIM I H AL-QATTAN',
                'username' => 'ibr20240001'
            ],
            [
                'student_id' => '20240002',
                'name_en' => 'YOUSEF ABDULAZIZ A A AL-MUHAIZA',
                'username' => 'ysf20240002'
            ],
            [
                'student_id' => '20240003',
                'name_en' => 'KHALID SAEDAN HD AL-MEJABA',
                'username' => 'khd20240003'
            ]
        ];

        foreach ($studentsData as $data) {
            $student = Student::where('student_id', $data['student_id'])->first();
            if ($student) {
                $student->update([
                    'name_en' => $data['name_en'],
                    'username' => $data['username']
                ]);
                echo "Updated student: {$student->name} -> {$data['name_en']} (Username: {$data['username']})\n";
            }
        }
    }
}