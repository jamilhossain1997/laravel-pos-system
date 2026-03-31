<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('products')->insert([
            [
                'name' => 'Rice',
                'unit_id' => 2,
                'buy_price' => 50,
                'sell_price' => 60,
                'stock' => 100,
            ],
            [
                'name' => 'Oil',
                'unit_id' => 3,
                'buy_price' => 120,
                'sell_price' => 140,
                'stock' => 50,
            ],
        ]);
    }
}