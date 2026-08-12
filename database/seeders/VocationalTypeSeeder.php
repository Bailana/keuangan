<?php

namespace Database\Seeders;

use App\Models\VocationalType;
use Illuminate\Database\Seeder;

class VocationalTypeSeeder extends Seeder
{
    public function run(): void
    {
        $vocationalTypes = [
            [
                'name' => 'Painting',
                'price_per_session' => 100000,
                'notes' => 'Pelatihan melukis dan seni rupa',
            ],
            [
                'name' => 'Komputer',
                'price_per_session' => 125000,
                'notes' => 'Pelatihan dasar komputer dan digital literacy',
            ],
            [
                'name' => 'Craft',
                'price_per_session' => 100000,
                'notes' => 'Pelatihan kerajinan tangan dan creative arts',
            ],
            [
                'name' => 'Beauty',
                'price_per_session' => 150000,
                'notes' => 'Pelatihan perawatan kecantikan dasar',
            ],
            [
                'name' => 'Gardening',
                'price_per_session' => 75000,
                'notes' => 'Pelatihan berkebun dan hortikultura',
            ],
            [
                'name' => 'House Keeping',
                'price_per_session' => 100000,
                'notes' => 'Pelatihan tata graha dan manajemen rumah',
            ],
            [
                'name' => 'Cooking',
                'price_per_session' => 150000,
                'notes' => 'Pelatihan memasak dan kuliner dasar',
            ],
        ];

        foreach ($vocationalTypes as $type) {
            VocationalType::firstOrCreate(
                ['name' => $type['name']],
                $type
            );
        }
    }
}
