<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WalletSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('wallets')->insert([
            [
                'name' => 'Bank Syariah Indonesia (BSI)',
                'slug' => 'bsi',
                'is_default' => true,
                'initial_balance' => 0,
                'owner_name' => '',
                'account_number' => '',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Bank Mandiri',
                'slug' => 'mandiri',
                'is_default' => false,
                'initial_balance' => 0,
                'owner_name' => '',
                'account_number' => '',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
