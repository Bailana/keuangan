<?php

namespace Database\Seeders;

use App\Models\TherapyType;
use Illuminate\Database\Seeder;

class TherapyTypeSeeder extends Seeder
{
    public function run(): void
    {
        $therapyTypes = [
            [
                'name' => 'Terapi Wicara',
                'price_per_session' => 150000,
                'notes' => 'Terapi untuk mengembangkan kemampuan bicara dan bahasa',
            ],
            [
                'name' => 'Sensori-Motor Integrasi',
                'price_per_session' => 175000,
                'notes' => 'Terapi untuk mengintegrasikan persepsi sensorik dan gerakan motorik',
            ],
            [
                'name' => 'Terapi Perilaku',
                'price_per_session' => 200000,
                'notes' => 'Terapi untuk memperbaiki perilaku dan social skills',
            ],
        ];

        foreach ($therapyTypes as $type) {
            TherapyType::firstOrCreate(
                ['name' => $type['name']],
                $type
            );
        }
    }
}
