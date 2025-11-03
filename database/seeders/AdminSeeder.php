<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Admin;
use App\Models\Admin as ModelsAdmin;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $admins = [
            [
                'name' => 'Super Administrator',
                'email' => 'admin@ju.edu.jo',
                'password' => 'password123',
                'role' => 'super_admin',
                'is_active' => true,
            ],
            [
                'name' => 'Academic Administrator',
                'email' => 'academic@ju.edu.jo',
                'password' => 'password123',
                'role' => 'academic_admin',
                'is_active' => true,
            ],
            [
                'name' => 'Finance Administrator',
                'email' => 'finance@ju.edu.jo',
                'password' => 'password123',
                'role' => 'finance_admin',
                'is_active' => true,
            ],
            [
                'name' => 'University Registrar',
                'email' => 'registrar@ju.edu.jo',
                'password' => 'password123',
                'role' => 'registrar',
                'is_active' => true,
            ],
        ];

        foreach ($admins as $admin) {
            Admin::create($admin);
        }
    }
}
