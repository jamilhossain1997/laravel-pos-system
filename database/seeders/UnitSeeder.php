<?php 
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\Unit;
 
class UnitSeeder extends Seeder {
    public function run(): void {
        $units = [
            ['name' => 'Piece',     'short_name' => 'PC'],
            ['name' => 'Kilogram',  'short_name' => 'KG'],
            ['name' => 'Gram',      'short_name' => 'GM'],
            ['name' => 'Liter',     'short_name' => 'LTR'],
            ['name' => 'Box',       'short_name' => 'BOX'],
            ['name' => 'Dozen',     'short_name' => 'DZN'],
            ['name' => 'Meter',     'short_name' => 'MTR'],
            ['name' => 'Set',       'short_name' => 'SET'],
        ];
        foreach ($units as $unit) {
            Unit::updateOrCreate(['short_name' => $unit['short_name']], $unit);
        }
    }
}