<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        Product::create([
            'name' => 'Laptop',
            'description' => 'High-performance laptop',
            'price' => 999.99,
            'quantity' => 10,
        ]);

        Product::create([
            'name' => 'Smartphone',
            'description' => 'Latest smartphone model',
            'price' => 699.99,
            'quantity' => 25,
        ]);

        Product::create([
            'name' => 'Headphones',
            'description' => 'Noise-cancelling headphones',
            'price' => 199.99,
            'quantity' => 50,
        ]);
    }
}