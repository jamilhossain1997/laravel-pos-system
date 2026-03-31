<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ClientSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('clients')->insert([
            ['name' => 'Walk-in Customer', 'phone' => null],
            ['name' => 'Regular Customer', 'phone' => '01800000000'],
        ]);
    }
}