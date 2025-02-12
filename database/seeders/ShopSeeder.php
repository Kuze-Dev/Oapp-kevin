<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class ShopSeeder extends Seeder
{
    public function run()
    {
        // Disable foreign key checks to allow truncation
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Truncate existing data
        DB::table('products')->truncate();
        DB::table('categories')->truncate();
        DB::table('brands')->truncate();

        // Enable foreign key checks again
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Insert Categories
        $categories = [
            ['name' => 'Electronics'],
            ['name' => 'Fashion'],
            ['name' => 'Home & Kitchen'],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }

        // Insert Brands
        $brands = [
            ['name' => 'Apple', 'brand_image' => 'apple_logo.png'],
            ['name' => 'Samsung', 'brand_image' => 'samsung_logo.png'],
            ['name' => 'Nike', 'brand_image' => 'nike_logo.png'],
            ['name' => 'Adidas', 'brand_image' => 'adidas_logo.png'],
            ['name' => 'No Brand', 'brand_image' => 'no_brand.png'], // Default for no-brand products
        ];

        foreach ($brands as $brand) {
            Brand::create($brand);
        }

        // Fetch newly created categories and brands
        $electronics = Category::where('name', 'Electronics')->first();
        $fashion = Category::where('name', 'Fashion')->first();
        $home = Category::where('name', 'Home & Kitchen')->first();

        $apple = Brand::where('name', 'Apple')->first();
        $samsung = Brand::where('name', 'Samsung')->first();
        $nike = Brand::where('name', 'Nike')->first();
        $adidas = Brand::where('name', 'Adidas')->first();
        $noBrand = Brand::where('name', 'No Brand')->first();

        // Insert Products
        $products = [
            [
                'name' => 'iPhone 14',
                'description' => 'Latest Apple iPhone 14 with A15 Bionic chip.',
                'product_image' => 'iphone14.jpg',
                'status' => 'Stock In',
                'category_id' => $electronics->id,
                'brand_id' => $apple->id,
            ],
            [
                'name' => 'Samsung Galaxy S23',
                'description' => 'High-end Samsung smartphone with advanced camera.',
                'product_image' => 'galaxy_s23.jpg',
                'status' => 'Stock In',
                'category_id' => $electronics->id,
                'brand_id' => $samsung->id,
            ],
            [
                'name' => 'Nike Air Max',
                'description' => 'Stylish and comfortable running shoes from Nike.',
                'product_image' => 'nike_air_max.jpg',
                'status' => 'Stock In',
                'category_id' => $fashion->id,
                'brand_id' => $nike->id,
            ],
            [
                'name' => 'Adidas Ultraboost',
                'description' => 'Performance running shoes with Boost technology.',
                'product_image' => 'ultraboost.jpg',
                'status' => 'Stock In',
                'category_id' => $fashion->id,
                'brand_id' => $adidas->id,
            ],
            [
                'name' => 'Air Fryer',
                'description' => 'Healthy air fryer for quick and easy meals.',
                'product_image' => 'air_fryer.jpg',
                'status' => 'Stock In',
                'category_id' => $home->id,
                'brand_id' => $noBrand->id, // Assign default "No Brand" instead of NULL
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }

        // Store inserted data in the session
        Session::put('seeded_categories', Category::all());
        Session::put('seeded_brands', Brand::all());
        Session::put('seeded_products', Product::all());
    }
}
