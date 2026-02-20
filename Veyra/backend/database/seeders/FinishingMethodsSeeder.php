<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FinishingMethodsSeeder extends Seeder
{
    public function run(): void
    {
        $methods = [
            'Continuous - natural fibers / fiber blends',
            'Continuous - synthetic fibers',
            'Discontinuous - natural fibers / fiber blends',
            'Discontinuous - synthetic fibers',
            'No pre-treatment',
            'Semi-continuous - natural fibers / fiber blends',
            'Semi-continuous - synthetic  fibers',
        ];

        $rows = array_map(fn ($name) => [
            'name' => $name,
            'created_at' => now(),
            'updated_at' => now(),
        ], $methods);

        DB::table('finishing_methods')->upsert($rows, ['name'], ['updated_at']);
    }
}
