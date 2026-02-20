<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FabricTypesSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            'Knitting - Circular',
            'Knitting - Flatbed 3D',
            'Knitting - Flatbed Fully Fashioned',
            'Knitting - Flatbed regular',
            'Knitting - Warp Raschel',
            'Knitting - Warp Tricot',
            'No knitting',
            'No weaving',
            'Non-woven, needle punch',
            'Non-woven, spun bonded',
            'Weaving - 3D',
            'Weaving - Air jet',
            'Weaving - Projectile',
            'Weaving - Rapier',
            'Weaving - Water jet',
        ];

        $rows = array_map(fn ($name) => [
            'name' => $name,
            'created_at' => now(),
            'updated_at' => now(),
        ], $items);

        DB::table('fabric_types')->upsert($rows, ['name'], ['updated_at']);
    }
}
