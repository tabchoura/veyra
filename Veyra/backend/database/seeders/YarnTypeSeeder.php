<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\YarnType;

class YarnTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            'Air-jet spinning for knitting, carded yarn',
            'Air-jet spinning for knitting, combed yarn',
            'Air-jet spinning for weaving, carded yarn',
            'Air-jet spinning for weaving, combed yarn',

            'Compact spinning for knitting, carded yarn',
            'Compact spinning for knitting, combed yarn',
            'Compact spinning for weaving, carded yarn',
            'Compact spinning for weaving, combed yarn',

            'Monofilament spinning for synthetic yarn',
            'Multifilament spinning of synthetic yarn',

            'No spinning 1',
            'No spinning 2',
            'No spinning 3',
            'No spinning 4',
            'No spinning 5',

            'Open end spinning for knitting, carded yarn',
            'Open end spinning for knitting, combed yarn',
            'Open end spinning for weaving, carded yarn',
            'Open end spinning for weaving, combed yarn',

            'Ring spinning for knitting, carded yarn',
            'Ring spinning for knitting, combed yarn',
            'Ring spinning for weaving, carded yarn',
            'Ring spinning for weaving, combed yarn',

            'Slub yarns for knitting, carded yarn',
            'Slub yarns for knitting, combed yarn',
            'Slub yarns for weaving, carded yarn',
            'Slub yarns for weaving, combed yarn',

            'Vortex spinning for knitting, carded yarn',
            'Vortex spinning for knitting, combed yarn',
            'Vortex spinning for weaving, carded yarn',
            'Vortex spinning for weaving, combed yarn',
        ];

        foreach ($types as $type) {
            YarnType::firstOrCreate([
                'name' => $type,
            ]);
        }

        $this->command?->info('✅ Yarn types seeded successfully (' . count($types) . ')');
    }
}
