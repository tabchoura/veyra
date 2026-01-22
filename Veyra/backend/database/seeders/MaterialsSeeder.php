<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Material;

class MaterialsSeeder extends Seeder
{
    /**
     * Seeder manuel - Liste complète des matériaux
     */
    public function run(): void
    {
        $materials = [
            ['name' => 'Acrylate coating', 'code' => null, 'region' => 'Global'],
            ['name' => 'Acrylic fiber', 'code' => 'PAC', 'region' => 'Global'],
            ['name' => 'Acrylic fiber, spun dyed', 'code' => 'PAC', 'region' => 'Global'],
            ['name' => 'Aramid fiber', 'code' => 'AR', 'region' => 'Global'],
            ['name' => 'Aramid fiber, spun dyed', 'code' => 'AR', 'region' => 'Global'],
            ['name' => 'BCI Cotton', 'code' => 'CO', 'region' => 'Bangladesh'],
            ['name' => 'BCI Cotton', 'code' => 'CO', 'region' => 'China'],
            ['name' => 'BCI Cotton', 'code' => 'CO', 'region' => 'Global'],
            ['name' => 'BCI Cotton', 'code' => 'CO', 'region' => 'India'],
            ['name' => 'BCI Cotton', 'code' => 'CO', 'region' => 'United States of America'],
            ['name' => 'Cotton', 'code' => 'CO', 'region' => 'Bangladesh'],
            ['name' => 'Cotton', 'code' => 'CO', 'region' => 'China'],
            ['name' => 'Cotton', 'code' => 'CO', 'region' => 'Global'],
            ['name' => 'Cotton', 'code' => 'CO', 'region' => 'India'],
            ['name' => 'Cotton', 'code' => 'CO', 'region' => 'United States of America'],
            ['name' => 'Cotton made in Africa', 'code' => 'CmIA', 'region' => 'Africa'],
            ['name' => 'Cotton, chemically recycled', 'code' => 'rCO', 'region' => 'Global'],
            ['name' => 'Cotton, chemically recycled, spun dyed', 'code' => 'rCO', 'region' => 'Global'],
            ['name' => 'Cotton, organic', 'code' => 'CO', 'region' => 'China'],
            ['name' => 'Cotton, organic', 'code' => 'CO', 'region' => 'Global'],
            ['name' => 'Cotton, organic', 'code' => 'CO', 'region' => 'India'],
            ['name' => 'Cotton, organic', 'code' => 'CO', 'region' => 'Türkiye'],
            ['name' => 'Cotton, organic', 'code' => 'CO', 'region' => 'United States of America'],
            ['name' => 'Cotton, post-consumer PurFi recycled', 'code' => 'rCO', 'region' => 'Global'],
            ['name' => 'Cotton, post-consumer chemical recycled SaXcell', 'code' => null, 'region' => 'Global'],
            ['name' => 'Cotton, post-consumer recycled', 'code' => 'rCO', 'region' => 'Global'],
            ['name' => 'Cotton, pre-consumer PurFi recycled', 'code' => 'rCO', 'region' => 'Global'],
            ['name' => 'Cotton, pre-consumer recycled', 'code' => 'rCO', 'region' => 'Global'],
            ['name' => 'Elastane fiber', 'code' => 'EL/EA', 'region' => 'Global'],
            ['name' => 'Elastane fiber, spun dyed', 'code' => 'EL/EA', 'region' => 'Global'],
            ['name' => 'HDPE fibers', 'code' => null, 'region' => 'Europe'],
            ['name' => 'HDPE fibers, spun dyed', 'code' => null, 'region' => 'Europe'],
            ['name' => 'HDPE granulates', 'code' => null, 'region' => 'Europe'],
            ['name' => 'HDPE granulates', 'code' => null, 'region' => 'Rest-of-World'],
            ['name' => 'Hemp fiber', 'code' => 'HA', 'region' => 'Global'],
            ['name' => 'Jute fiber', 'code' => 'JU', 'region' => 'Global'],
            ['name' => 'Kenaf fiber', 'code' => 'KE', 'region' => 'Global'],
            ['name' => 'LDPE fibers', 'code' => null, 'region' => 'Europe'],
            ['name' => 'LDPE fibers, spun dyed', 'code' => null, 'region' => 'Europe'],
            ['name' => 'LDPE granulates', 'code' => null, 'region' => 'Europe'],
            ['name' => 'LDPE granulates', 'code' => null, 'region' => 'Global'],
            ['name' => 'Linen fibers', 'code' => 'LI', 'region' => 'Global'],
            ['name' => 'Lyocell viscose - Modal', 'code' => 'CMD', 'region' => 'Global'],
            ['name' => 'Lyocell viscose - Modal, spun dyed', 'code' => 'CMD', 'region' => 'Global'],
            ['name' => 'Lyocell viscose - Refibra', 'code' => 'CLY', 'region' => 'Global'],
            ['name' => 'Lyocell viscose - Refibra, spun dyed', 'code' => 'CLY', 'region' => 'Global'],
            ['name' => 'No impact material', 'code' => null, 'region' => 'Global'],
            ['name' => 'No impact material [kg]', 'code' => null, 'region' => 'Global'],
            ['name' => 'Other recycled fibers', 'code' => null, 'region' => 'Global'],
            ['name' => 'PET fibers', 'code' => null, 'region' => 'Europe'],
            ['name' => 'PET fibers, chemically recycled', 'code' => 'rPET', 'region' => 'Global'],
            ['name' => 'PET fibers, chemically recycled, spun dyed', 'code' => 'rPET', 'region' => 'Global'],
            ['name' => 'PET fibers, extrusion recycled', 'code' => 'rPET', 'region' => 'Global'],
            ['name' => 'PET fibers, post-consumer recycled', 'code' => 'rPET', 'region' => 'Global'],
            ['name' => 'PET fibers, post-consumer recycled, spun dyed', 'code' => 'rPET', 'region' => 'Global'],
            ['name' => 'PET fibers, pre-consumer recycled', 'code' => 'rPET', 'region' => 'Global'],
            ['name' => 'PET fibers, pre-consumer recycled, spun dyed', 'code' => 'rPET', 'region' => 'Global'],
            ['name' => 'PET fibers, spun dyed', 'code' => null, 'region' => 'Europe'],
            ['name' => 'PET granulates', 'code' => null, 'region' => 'Europe'],
            ['name' => 'PET granulates', 'code' => null, 'region' => 'Global'],
            ['name' => 'PET laminate', 'code' => null, 'region' => 'Global'],
            ['name' => 'PLA fibers', 'code' => 'PLA', 'region' => 'Global'],
            ['name' => 'PLA fibers, spun dyed', 'code' => 'PLA', 'region' => 'Global'],
            ['name' => 'PLA granulates', 'code' => null, 'region' => 'Global'],
            ['name' => 'PP fibers', 'code' => null, 'region' => 'Europe'],
            ['name' => 'PP fibers, spun dyed', 'code' => null, 'region' => 'Europe'],
            ['name' => 'PP granulates', 'code' => null, 'region' => 'Europe'],
            ['name' => 'PP granulates', 'code' => null, 'region' => 'Global'],
            ['name' => 'PTFE laminate', 'code' => null, 'region' => 'Global'],
            ['name' => 'PU coating', 'code' => null, 'region' => 'Global'],
            ['name' => 'PU laminate', 'code' => null, 'region' => 'Global'],
            ['name' => 'PVC coating', 'code' => null, 'region' => 'Global'],
            ['name' => 'PVC fibers', 'code' => null, 'region' => 'Global'],
            ['name' => 'PVC fibers, spun dyed', 'code' => null, 'region' => 'Global'],
            ['name' => 'PVC granulates', 'code' => null, 'region' => 'Global'],
            ['name' => 'Paper, recycled', 'code' => null, 'region' => 'Global'],
            ['name' => 'Polyamide 6 fibers', 'code' => 'PA 6', 'region' => 'Global'],
            ['name' => 'Polyamide 6 fibers, spun dyed', 'code' => 'PA 6', 'region' => 'Global'],
            ['name' => 'Polyamide 6-6 fibers', 'code' => 'PA 6.6', 'region' => 'Global'],
            ['name' => 'Polyamide 6-6 fibers, spun dyed', 'code' => 'PA 6.6', 'region' => 'Global'],
            ['name' => 'Polyamide fibers, chemical recycled', 'code' => null, 'region' => 'Global'],
            ['name' => 'Polyester fiber', 'code' => 'PES', 'region' => 'Global'],
            ['name' => 'Polyester fiber, spun dyed', 'code' => 'PES', 'region' => 'Global'],
            ['name' => 'Silicone, moulded', 'code' => null, 'region' => 'Global'],
            ['name' => 'TPU laminate', 'code' => null, 'region' => 'Global'],
            ['name' => 'Top', 'code' => null, 'region' => 'Global'],
            ['name' => 'Viscose', 'code' => 'CV', 'region' => 'Asia'],
            ['name' => 'Viscose', 'code' => 'CV', 'region' => 'Austria'],
            ['name' => 'Viscose Bamboo', 'code' => 'CV', 'region' => 'China'],
            ['name' => 'Viscose Lyocell', 'code' => 'CLY', 'region' => 'Global'],
            ['name' => 'Viscose Lyocell, spun dyed', 'code' => 'CLY', 'region' => 'Global'],
            ['name' => 'Viscose fiber', 'code' => 'CV', 'region' => 'Global'],
            ['name' => 'Viscose fiber, spun dyed', 'code' => 'CV', 'region' => 'Global'],
            ['name' => 'Viscose, spun dyed', 'code' => 'CV', 'region' => 'Asia'],
            ['name' => 'Viscose, spun dyed', 'code' => 'CV', 'region' => 'Austria'],
            ['name' => 'Viscose, spun dyed Bamboo', 'code' => 'CV', 'region' => 'China'],
            ['name' => 'Wool', 'code' => 'WO', 'region' => 'Global'],
            ['name' => 'Wool', 'code' => 'WO', 'region' => 'United States of America'],
            ['name' => 'Wool, post-consumer recycled', 'code' => 'rWO', 'region' => 'Global'],
            ['name' => 'Wool, pre-consumer recycled', 'code' => 'rWO', 'region' => 'Global'],
        ];

        $this->command->info('🔄 Importation en cours...');
        
        foreach ($materials as $material) {
            Material::updateOrCreate(
                ['name' => $material['name'], 'region' => $material['region']],
                ['code' => $material['code']]
            );
        }

        $this->command->info('✅ ' . count($materials) . ' matériaux créés!');
    }
}