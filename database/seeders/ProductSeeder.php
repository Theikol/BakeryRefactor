<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            [
                'name' => 'Roti Tawar',
                'category' => 'Roti',
                'price' => 15000,
                'stock' => 50,
                'description' => 'Roti tawar segar dengan tekstur lembut dan rasa yang enak.',
                'is_active' => true,
            ],
            [
                'name' => 'Kue Brownies',
                'category' => 'Kue',
                'price' => 25000,
                'stock' => 30,
                'description' => 'Brownies coklat dengan tekstur lembut dan rasa manis yang pas.',
                'is_active' => true,
            ],
            [
                'name' => 'Croissant',
                'category' => 'Pastry',
                'price' => 18000,
                'stock' => 25,
                'description' => 'Croissant buttery yang renyah di luar dan lembut di dalam.',
                'is_active' => true,
            ],
            [
                'name' => 'Donat Coklat',
                'category' => 'Donat',
                'price' => 12000,
                'stock' => 40,
                'description' => 'Donat dengan topping coklat yang lezat dan menggugah selera.',
                'is_active' => true,
            ],
            [
                'name' => 'Baguette',
                'category' => 'Roti',
                'price' => 20000,
                'stock' => 20,
                'description' => 'Baguette Prancis asli dengan kulit yang renyah.',
                'is_active' => true,
            ],
            [
                'name' => 'Cupcake Vanilla',
                'category' => 'Kue',
                'price' => 15000,
                'stock' => 35,
                'description' => 'Cupcake vanilla dengan frosting yang creamy dan manis.',
                'is_active' => true,
            ],
            [
                'name' => 'Pain au Chocolat',
                'category' => 'Pastry',
                'price' => 22000,
                'stock' => 15,
                'description' => 'Pain au chocolat dengan coklat premium di dalamnya.',
                'is_active' => true,
            ],
            [
                'name' => 'Donat Stroberi',
                'category' => 'Donat',
                'price' => 13000,
                'stock' => 45,
                'description' => 'Donat dengan topping stroberi yang segar dan manis.',
                'is_active' => true,
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}