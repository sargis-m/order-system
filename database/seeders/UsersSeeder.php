<?php

namespace Database\Seeders;

use App\Models\Constants\Role;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UsersSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin@test.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('admin123')
            ]
        );
        if (!$admin->hasRole(Role::ADMIN)) {
            $admin->assignRole(Role::ADMIN);
        }

        $partners = [];
        for ($i = 1; $i <= 3; $i++) {
            $partner = User::firstOrCreate(
                ['email' => "partner{$i}@test.com"],
                [
                    'name' => "Partner $i",
                    'password' => Hash::make('partner123')
                ]
            );
            if (!$partner->hasRole(Role::PARTNER)) {
                $partner->assignRole(Role::PARTNER);
            }

            for ($j = 1; $j <= 3; $j++) {
                $customer = User::firstOrCreate(
                    ['email' => "customer_p{$i}_{$j}@test.com"],
                    [
                        'name' => "Customer P{$i}-{$j}",
                        'password' => Hash::make('customer123'),
                        'partner_id' => $partner->id,
                    ]
                );
                if (!$customer->hasRole(Role::CUSTOMER)) {
                    $customer->assignRole(Role::CUSTOMER);
                }
                if (!$customer->partner_id) {
                    $customer->update(['partner_id' => $partner->id]);
                }
            }
        }

        $extraCustomer = User::firstOrCreate(
            ['email' => 'customer@test.com'],
            [
                'name' => 'Customer Extra',
                'password' => Hash::make('customer123')
            ]
        );
        if (!$extraCustomer->hasRole(Role::CUSTOMER)) {
            $extraCustomer->assignRole(Role::CUSTOMER);
        }
    }
}
