<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        foreach ([
            ['name' => 'Camiseta', 'description' => 'Camiseta de algodon', 'price_cents' => 1500],
            ['name' => 'Mochila', 'description' => 'Mochila para uso diario', 'price_cents' => 3500],
            ['name' => 'Botella reutilizable', 'description' => 'Botella de acero inoxidable', 'price_cents' => 1800],
        ] as $product) {
            Product::updateOrCreate(['name' => $product['name']], $product + ['active' => true]);
        }
    }
}
