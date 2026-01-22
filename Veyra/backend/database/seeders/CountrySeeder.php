<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Country;

class CountrySeeder extends Seeder
{
    public function run(): void
    {
        // ✅ plus safe que delete() si FK / auto-increment
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('countries')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $countries = [
            'Africa','Albania','Algeria','Asia','Australia','Austria','Bangladesh','Belarus',
            'Belgium','Bosnia and Herzegovina','Brazil','Bulgaria','Cambodia','Canada',
            'Canada without Quebec','China','Croatia','Czech Republic','Denmark','Egypt',
            'Estonia','Ethiopia','Europe','Finland','France','Germany','Global','Greece',
            'Hong Kong','Hungary','Iceland','India','Indonesia','Iran','Ireland','Israel',
            'Italy','Japan','Jordan','Kazakhstan','Kenya','Korea','Kyrgyzstan','Latvia',
            'Lebanon','Lithuania','Luxembourg','Macedonia','Madagascar','Malaysia',
            'Mauritius','Mexico','Moldova','Morocco','Myanmar','Nepal','Netherlands',
            'New Zealand','Nicaragua','North Africa','North America','Norway','Oceania',
            'Pakistan','Peru','Philippines','Poland','Portugal','Romania','Russia',
            'Saudi Arabia','Serbia','Singapore','Slovakia','Slovenia','South Africa',
            'South America','Spain','Sri Lanka','Sweden','Switzerland','Syria','Taiwan',
            'Tanzania','Thailand','Tunisia','Turkey','Uganda','Ukraine',
            'United Arab Emirates','United Kingdom','United States','Uruguay','Uzbekistan','Vietnam',
        ];

        foreach ($countries as $name) {
            Country::create([
                'name_en' => $name,
                'is_active' => true,
            ]);
        }

        $this->command?->info('✅ Countries seeded successfully!');
    }
}
