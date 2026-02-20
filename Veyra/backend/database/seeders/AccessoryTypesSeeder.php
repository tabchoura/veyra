<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class  AccessoryTypesSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            'Sewing repairs',
            'Hand stitching',
            'Machine stitching',
            'Patch repair / adding a patch',
            'Darning (for holes)',
            'Mending seams',
            'Restitching loose seams',
            'Hem repair / re-hemming',
            'Zipper replacement',
            'Zipper slider replacement',
            'Button replacement',
            'Reattaching snaps or hooks',
            'Replacing elastic',
            'Repairing torn fabric',
            'Fixing ripped pockets',
            'Reinforcing weak areas',
            'Repairing lining',
            'Adjusting waistband',
            'Fixing or replacing drawcords',
            'Replacing or repairing eyelets',
            'Repairing cuffs and collars',
            'Removing lint or pilling',
            'Restoring shape with pressing or steaming',
            'Repairing appliqués or patches',
            'Reattaching labels',
        ];

        $rows = array_map(fn ($name) => [
            'name' => $name,
            'created_at' => now(),
            'updated_at' => now(),
        ], $items);

        // no duplicates if you re-run seeding
        DB::table('accessory_types')->upsert($rows, ['name'], ['updated_at']);
    }
}
