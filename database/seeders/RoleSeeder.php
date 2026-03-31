<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\Role;
 
class RoleSeeder extends Seeder {
    public function run(): void {
        $roles = [
            [
                'name' => 'Super Admin',
                'slug' => 'super-admin',
                'permissions' => json_encode(['*']), // all permissions
            ],
            [
                'name' => 'Manager',
                'slug' => 'manager',
                'permissions' => json_encode([
                    'dashboard','pos','clients','invoices','quotations',
                    'products','barcodes','income','expense','reports',
                ]),
            ],
            [
                'name' => 'Cashier',
                'slug' => 'cashier',
                'permissions' => json_encode(['dashboard','pos','clients','invoices']),
            ],
        ];
        foreach ($roles as $role) {
            Role::updateOrCreate(['slug' => $role['slug']], $role);
        }
    }
}