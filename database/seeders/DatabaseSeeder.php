<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Child;
use App\Models\IncomeCategory;
use App\Models\ExpenseCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $this->call([
            IncomeCategorySeeder::class,
            ExpenseCategorySeeder::class,
            SuperAdminSeeder::class,
        ]);
        User::updateOrCreate(
            ['email' => 'admin@mandiri.com'],
            [
                'name' => 'Administrator',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ]
        );

        User::updateOrCreate(
            ['email' => 'viewer@mandiri.com'],
            [
                'name' => 'Viewer',
                'password' => Hash::make('password'),
                'role' => 'viewer',
            ]
        );

        $children = [
            [
                'name' => 'Ahmad Fauzi',
                'parent_name' => 'Bp. Hendra & Ibu Siti Aminah',
                'parent_whatsapp' => '081234567890',
                'class_name' => 'Kelas Terapi Intensif A',
                'monthly_fee' => 1500000,
            ],
            [
                'name' => 'Siti Aisyah',
                'parent_name' => 'Bp. Rahman & Ibu Fatimah',
                'parent_whatsapp' => '081234567891',
                'class_name' => 'TK 1',
                'monthly_fee' => 1500000,
            ],
            [
                'name' => 'Budi Pratama',
                'parent_name' => 'Bp. Surya & Ibu Dewi',
                'parent_whatsapp' => '081234567892',
                'class_name' => 'Workshop Vokasi 2',
                'monthly_fee' => 1000000,
            ],
            [
                'name' => 'Dewi Lestari',
                'parent_name' => 'Bp. Joko & Ibu Rina',
                'parent_whatsapp' => '081234567893',
                'class_name' => 'Terapi + Sekolah',
                'monthly_fee' => 2500000,
            ],
        ];

        foreach ($children as $data) {
            $existing = Child::where('name', $data['name'])->first();
            if ($existing) {
                $existing->update($data);
            } else {
                Child::create($data);
            }
        }
    }
}
