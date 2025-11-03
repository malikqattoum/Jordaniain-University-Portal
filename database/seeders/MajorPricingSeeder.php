<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MajorPricing;

class MajorPricingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $pricingData = [
            [
                'major_name' => 'القانون',
                'major_key' => 'law',
                'hourly_rate' => 30.00,
                'currency' => 'USD',
                'is_active' => true,
                'description' => 'كلية الحقوق - تخصص القانون'
            ],
            [
                'major_name' => 'الهندسة',
                'major_key' => 'engineering',
                'hourly_rate' => 35.00,
                'currency' => 'USD',
                'is_active' => true,
                'description' => 'كلية الهندسة - جميع التخصصات الهندسية'
            ],
            [
                'major_name' => 'الطب',
                'major_key' => 'medicine',
                'hourly_rate' => 50.00,
                'currency' => 'USD',
                'is_active' => true,
                'description' => 'كلية الطب البشري'
            ],
            [
                'major_name' => 'الصيدلة',
                'major_key' => 'pharmacy',
                'hourly_rate' => 40.00,
                'currency' => 'USD',
                'is_active' => true,
                'description' => 'كلية الصيدلة'
            ],
            [
                'major_name' => 'طب الأسنان',
                'major_key' => 'dentistry',
                'hourly_rate' => 45.00,
                'currency' => 'USD',
                'is_active' => true,
                'description' => 'كلية طب الأسنان'
            ],
            [
                'major_name' => 'التمريض',
                'major_key' => 'nursing',
                'hourly_rate' => 25.00,
                'currency' => 'USD',
                'is_active' => true,
                'description' => 'كلية التمريض'
            ],
            [
                'major_name' => 'العلوم',
                'major_key' => 'science',
                'hourly_rate' => 28.00,
                'currency' => 'USD',
                'is_active' => true,
                'description' => 'كلية العلوم - جميع التخصصات العلمية'
            ],
            [
                'major_name' => 'الآداب',
                'major_key' => 'arts',
                'hourly_rate' => 22.00,
                'currency' => 'USD',
                'is_active' => true,
                'description' => 'كلية الآداب - جميع التخصصات الأدبية'
            ],
            [
                'major_name' => 'إدارة الأعمال',
                'major_key' => 'business',
                'hourly_rate' => 26.00,
                'currency' => 'USD',
                'is_active' => true,
                'description' => 'كلية إدارة الأعمال'
            ],
            [
                'major_name' => 'التربية',
                'major_key' => 'education',
                'hourly_rate' => 24.00,
                'currency' => 'USD',
                'is_active' => true,
                'description' => 'كلية التربية - جميع التخصصات التعليمية'
            ]
        ];

        foreach ($pricingData as $data) {
            MajorPricing::updateOrCreate(
                ['major_key' => $data['major_key']],
                $data
            );
        }
    }
}
