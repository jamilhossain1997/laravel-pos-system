<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
 
class UserSeeder extends Seeder {
    public function run(): void {
        User::updateOrCreate(['email' => 'admin@pos.com'], [
            'name'     => 'Super Admin',
            'email'    => 'admin@pos.com',
            'password' => Hash::make('password'),
            'role_id'  => 1,
            'is_active'=> true,
        ]);
        User::updateOrCreate(['email' => 'manager@pos.com'], [
            'name'     => 'Store Manager',
            'email'    => 'manager@pos.com',
            'password' => Hash::make('password'),
            'role_id'  => 2,
            'is_active'=> true,
        ]);
        User::updateOrCreate(['email' => 'cashier@pos.com'], [
            'name'     => 'Cashier 1',
            'email'    => 'cashier@pos.com',
            'password' => Hash::make('password'),
            'role_id'  => 3,
            'is_active'=> true,
        ]);
    }
}