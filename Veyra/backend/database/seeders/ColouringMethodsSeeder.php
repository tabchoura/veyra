<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ColouringMethodsSeeder extends Seeder
{
    public function run(): void
    {
        $methods = [
            'Air-jet dyeing - natural fibers / fiber blends',
            'Air-jet dyeing - synthetic fibers',
            'Drying / Fixation',
            'Ink-jet printing',
            'Jet dyeing - natural fibers / fiber blends',
            'Jet dyeing - synthetic fibers',
            'Jigger dyeing',
            'No dyeing or printing',
            'Pad-batch dyeing',
            'Pad-steam dyeing',
            'Pigment printing',
            'Printing',
            'Transfer printing',
        ];

        $rows = array_map(fn ($name) => [
            'name' => $name,
            'created_at' => now(),
            'updated_at' => now(),
        ], $methods);

        DB::table('colouring_methods')->upsert($rows, ['name'], ['updated_at']);
    }
}
