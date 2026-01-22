<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Subcategory;

class CategorySeeder extends Seeder
{
    public function run()
    {
        $this->command->info('📦 Seeding categories and subcategories...');

        $categories = [
            'Electronics' => ['Smartphones', 'Laptops', 'Tablets', 'Cameras', 'Accessories'],
            'Clothing' => ['Men', 'Women', 'Kids', 'Shoes', 'Accessories'],
            'Food & Beverages' => ['Fresh Produce', 'Dairy', 'Meat', 'Beverages', 'Snacks'],
            'Home & Garden' => ['Furniture', 'Decor', 'Kitchen', 'Bathroom', 'Garden'],
            'Sports & Outdoors' => ['Fitness', 'Camping', 'Sports Equipment', 'Cycling', 'Outdoor Gear'],
            'Books & Media' => ['Books', 'Movies', 'Music', 'Games', 'Magazines'],
            'Toys & Hobbies' => ['Action Figures', 'Dolls', 'Board Games', 'Puzzles', 'Crafts'],
            'Health & Beauty' => ['Skincare', 'Makeup', 'Hair Care', 'Fragrances', 'Supplements'],
        ];

        foreach ($categories as $categoryName => $subcategories) {
            $category = Category::create(['name' => $categoryName]);
            
            foreach ($subcategories as $subcategoryName) {
                Subcategory::create([
                    'category_id' => $category->id,
                    'name' => $subcategoryName,
                ]);
            }
            
            $this->command->info("✅ Created category: {$categoryName} with " . count($subcategories) . " subcategories");
        }

        $this->command->info('✅ Seeding completed successfully!');
    }
}