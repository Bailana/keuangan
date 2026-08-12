<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ExpenseCategory;

class ExpenseCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Gaji Karyawan', 'description' => 'Pembayaran gaji karyawan'],
            ['name' => 'SPP', 'description' => 'Pembayaran SPP sekolah'],
            ['name' => 'Lain-lain', 'description' => 'Pengeluaran lainnya'],
            ['name' => 'BPJS Kesehatan', 'description' => 'Iuran BPJS Kesehatan'],
            ['name' => 'BPJS Ketenagakerjaan', 'description' => 'Iuran BPJS Ketenagakerjaan'],
            ['name' => 'Inklusi', 'description' => 'Biaya inklusi'],
            ['name' => 'Pulsa & Pascabayar', 'description' => 'Tagihan pulsa & pascabayar'],
            ['name' => 'Internet', 'description' => 'Tagihan internet'],
            ['name' => 'Listrik', 'description' => 'Tagihan listrik'],
            ['name' => 'Terapi', 'description' => 'Pembayaran layanan terapi'],
            ['name' => 'Tunjangan', 'description' => 'Tunjangan karyawan'],
        ];

        foreach ($categories as $category) {
            ExpenseCategory::updateOrCreate(
                ['name' => $category['name']],
                ['description' => $category['description']]
            );
        }
    }
}
