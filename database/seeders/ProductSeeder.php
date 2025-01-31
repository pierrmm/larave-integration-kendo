<?php

namespace Database\Seeders;

use App\Models\products;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Products::create([
            'name' => 'nununk69',
            'description' => 'Description 1',
            'price' => 500000,
            'image' => 'https://via.placeholder.com/150',
        ]);
    }
}
