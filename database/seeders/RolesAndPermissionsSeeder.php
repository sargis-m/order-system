<?php

namespace Database\Seeders;

use App\Models\Constants\Permission;
use App\Models\Constants\Role;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role as SpatieRole;
use Spatie\Permission\Models\Permission as SpatiePermission;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            Permission::ACCEPT_ORDERS,
            Permission::REJECT_ORDERS,
            Permission::PLACE_ORDER,
            Permission::REGISTER_CUSTOMER,
            Permission::VIEW_OWN_ORDERS,
            Permission::VIEW_ALL_ORDERS,
        ];

        foreach ($permissions as $perm) {
            SpatiePermission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
        }

        $admin = SpatieRole::firstOrCreate(['name' => Role::ADMIN, 'guard_name' => 'web']);
        $partner = SpatieRole::firstOrCreate(['name' => Role::PARTNER, 'guard_name' => 'web']);
        $customer = SpatieRole::firstOrCreate(['name' => Role::CUSTOMER, 'guard_name' => 'web']);

        $admin->givePermissionTo([
            Permission::ACCEPT_ORDERS,
            Permission::REJECT_ORDERS,
            Permission::VIEW_ALL_ORDERS
        ]);
        $partner->givePermissionTo([
            Permission::PLACE_ORDER,
            Permission::REGISTER_CUSTOMER,
            Permission::VIEW_OWN_ORDERS
        ]);
        $customer->givePermissionTo([
            Permission::PLACE_ORDER,
            Permission::VIEW_OWN_ORDERS
        ]);
    }
}
