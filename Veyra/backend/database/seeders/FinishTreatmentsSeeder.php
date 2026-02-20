<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FinishTreatmentsSeeder extends Seeder
{
    public function run(): void
    {
        $treatments = [
            'Chemical bleaching',
            'Decatizing',
            'Drying',
            'Embroidery',
            'Enzyme wash',
            'Garment dyeing',
            'Ironing',
            'Laser finishing',
            'Mechanical treatment (jeans)',
            'Nano/plasma-finishing',
            'No product finishing',
            'Ozone treatment',
            'Screen print (large)',
            'Screen print (small)',
            'Stone wash',
            'Transfer print',
            'Washing',
        ];

        $rows = array_map(fn ($name) => [
            'name' => $name,
            'created_at' => now(),
            'updated_at' => now(),
        ], $treatments);

        DB::table('finish_treatments')->upsert($rows, ['name'], ['updated_at']);
    }
}
