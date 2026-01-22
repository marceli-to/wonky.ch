<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the Tisch category
        $tableCategory = Category::where('slug', 'tisch')->first();

        $products = [
            [
                'name' => 'Bistrot',
                'description' => 'Eleganter Massivholztisch mit zeitlosem Design',
                'price' => 1200.00,
                'stock' => 5,
                'published' => true,
            ],
            [
                'name' => 'Caprea',
                'description' => 'Moderner Tisch mit klaren Linien und robuster Bauweise',
                'price' => 1650.00,
                'stock' => 3,
                'published' => true,
            ],
            [
                'name' => 'Novatur',
                'description' => 'Minimalistischer Tisch aus nachhaltigem Holz',
                'price' => 1800.00,
                'stock' => 4,
                'published' => true,
            ],
        ];

        foreach ($products as $productData) {
            $product = Product::create([
                'uuid' => (string) Str::uuid(),
                'name' => $productData['name'],
                'slug' => Str::slug($productData['name']),
                'description' => $productData['description'],
                'price' => $productData['price'],
                'stock' => $productData['stock'],
                'published' => $productData['published'],
            ]);

            // Attach to Tisch category
            if ($tableCategory) {
                $product->categories()->attach($tableCategory->id);
            }
        }
    }
}
