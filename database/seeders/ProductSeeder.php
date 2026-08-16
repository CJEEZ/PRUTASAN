<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Define Categories and use updateOrCreate for robustness
        $categoriesData = [
            ['name' => 'Seasonal', 'slug' => 'seasonal'],
            ['name' => 'Tropical', 'slug' => 'tropical'],
            ['name' => 'Exotic', 'slug' => 'exotic'],
        ];

        foreach ($categoriesData as $categoryData) {
            // Use updateOrCreate to ensure the category exists and its data is correct
            Category::updateOrCreate(['slug' => $categoryData['slug']], $categoryData);
        }

        // Fetch IDs after creation (Ensure they exist)
        // If categories still can't be found here, the issue is likely with the Category model or database connection.
        try {
            $seasonalId = Category::where('slug', 'seasonal')->firstOrFail()->id;
            $tropicalId = Category::where('slug', 'tropical')->firstOrFail()->id;
            $exoticId = Category::where('slug', 'exotic')->firstOrFail()->id;
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            // Log an error if categories are missing to help diagnose the problem
            \Log::error("Product Seeder failed: Required categories (seasonal, tropical, exotic) not found after creation.", ['error' => $e->getMessage()]);
            // If running via artisan, this message will be visible
            throw new \Exception("Seeder failed because required categories could not be found. Check your Category model and database connection.");
        }

        // 2. Define Products with consistent image URLs
        $products = [
            // --- CORE PRODUCTS ---
            [
                'name' => 'Premium Carabao Mango',
                'description' => 'Sweet and juicy Philippine Carabao mango, the best in the world.',
                'price' => 180.00,
                'unit' => '1kg',
                'image_url' => 'https://mangoesmagic.com/wp-content/uploads/2024/07/Philippine-mango-on-display-and-for-sale-at-a-local-market.jpg',
                'category_id' => $tropicalId,
                'is_seasonal' => true,
                'is_exotic' => false,
            ],
            [
                'name' => 'Sweet Latundan Banana',
                'description' => 'A dozen of the best Latundan bananas, perfect for snacks.',
                'price' => 65.00,
                'unit' => '1 dozen',
                'image_url' => 'https://www.thedailymeal.com/img/gallery/the-surprising-history-of-the-banana/Main%20-ThinkstockPhotos-182199425.jpg',
                'category_id' => $tropicalId,
                'is_seasonal' => false, // All Year
                'is_exotic' => false,
            ],
            [
                'name' => 'Fresh Pineapple Queen',
                'description' => 'A single Queen pineapple, known for its extra sweetness and crispness.',
                'price' => 95.00,
                'unit' => '1 piece',
                'image_url' => 'https://media.self.com/photos/5b4371cc4d0c3c282a8878d3/16:9/w_4544,h_2556,c_limit/pineapple.jpg',
                'category_id' => $tropicalId,
                'is_seasonal' => true,
                'is_exotic' => true,
            ],
            [
                'name' => 'Ripe Papaya',
                'description' => 'Large, fully ripened papaya, ready to eat.',
                'price' => 120.00,
                'unit' => '2kg',
                'image_url' => 'https://kannada.cdn.zeenews.com/kannada/sites/default/files/styles/zm_700x400/public/2023/12/13/359780-papaya.jpg?itok=sMwb83pO',
                'category_id' => $tropicalId,
                'is_seasonal' => false, // All Year
                'is_exotic' => false,
            ],
            [
                'name' => 'Dalandan',
                'description' => 'Locally grown Calamansi, essential for everyday cooking and drinks.',
                'price' => 45.00,
                'unit' => '500g',
                'image_url' => 'https://tse3.mm.bing.net/th/id/OIP.S4KHlb_bSX0Xfhmywp-1kwHaE5?cb=ucfimg2&ucfimg=1&rs=1&pid=ImgDetMain&o=7&rm=3',
                'category_id' => $seasonalId,
                'is_seasonal' => false,
                'is_exotic' => false,
            ],
            [
                'name' => 'Avocado',
                'description' => 'Freshly picked organic strawberries.',
                'price' => 350.00,
                'unit' => '250g box',
                'image_url' => 'https://learnhowtosign.com/wp-content/uploads/2024/05/Dictionary_avocado-1-2.jpg',
                'category_id' => $exoticId,
                'is_seasonal' => true,
                'is_exotic' => false,
            ],


            [
                'name' => 'Sweet Watermelon',
                'description' => 'A giant, sweet, and crisp seedless watermelon.',
                'price' => 250.00,
                'unit' => '1 piece',
                'image_url' => 'https://seedsofplenty.com.au/cdn/shop/products/WATERMELON-Bush-Jubilee.jpg?v=1670216488&width=713',
                'category_id' => $tropicalId,
                'is_seasonal' => true,
                'is_exotic' => false,
            ],
            [
                'name' => 'Young Coconut (Buko)',
                'description' => 'Fresh Buko, great for hydration and its tender meat.',
                'price' => 50.00,
                'unit' => '1 piece',
                'image_url' => 'https://ineedmedic.com/wp-content/uploads/2020/02/how-healthy-is-coconut-sugar.jpg',
                'category_id' => $tropicalId,
                'is_seasonal' => false,
                'is_exotic' => false,
            ],
            [
                'name' => 'Exotic Dragon Fruit (Red)',
                'description' => 'Vibrant red dragon fruit, rich in antioxidants. Truly exotic!',
                'price' => 190.00,
                'unit' => '500g',
                'image_url' => 'https://healthyfamilyproject.com/wp-content/uploads/2020/05/Dragonfruit-background.jpg',
                'category_id' => $tropicalId,
                'is_seasonal' => true,
                'is_exotic' => true,
            ],
            [
                'name' => 'Pomelo (Davao)',
                'description' => 'Large, juicy, and less acidic pomelo from Southern Philippines.',
                'price' => 170.00,
                'unit' => '1 piece',
                'image_url' => 'https://images.onlymyhealth.com/imported/images/2024/January/31_Jan_2024/pomelo.jpg',
                'category_id' => $seasonalId,
                'is_seasonal' => true,
                'is_exotic' => false,
            ],
            [
                'name' => 'Rambutan',
                'description' => 'High-quality imported blueberries in a convenient pack.',
                'price' => 420.00,
                'unit' => '125g box',
                'image_url' => 'https://umsu.ac.id/health/wp-content/uploads/2023/08/Manfaat-Rambutan-untuk-Kesehatan.jpg',
                'category_id' => $exoticId,
                'is_seasonal' => false,
                'is_exotic' => true, // Considered exotic for the region
            ],
            [
                'name' => 'Lanzones (Seasonal)',
                'description' => 'Sweet and sour Lanzones, a Filipino seasonal favorite.',
                'price' => 135.00,
                'unit' => '1kg',
                'image_url' => 'https://images.saymedia-content.com/.image/t_share/MTc0OTk3MDQ0NDY0MDY4MDM2/list-of-round-or-circular-fruits.jpg',
                'category_id' => $tropicalId,
                'is_seasonal' => true,
                'is_exotic' => false,
            ],
        ];

        // Use updateOrCreate to prevent duplicates and ensure all fields are set correctly
        foreach ($products as $productData) {
            Product::updateOrCreate(['name' => $productData['name']], $productData);
        }
    }
}
