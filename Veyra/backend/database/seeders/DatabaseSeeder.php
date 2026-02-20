<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            SuperAdminSeeder::class,
            YarnTypeSeeder::class,
            CategorySeeder::class,
            CountrySeeder::class,
            FabricTypesSeeder::class,
            MaterialsSeeder::class,
            FinishingMethodsSeeder::class,
            FinishTreatmentsSeeder::class,
            AccessoryTypesSeeder::class,
            ColouringMethodsSeeder::class,
             

        ]);
    }
}
