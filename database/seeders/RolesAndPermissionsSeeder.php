<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run()
    {
        // Create roles
        $roles = [
            [
                'name' => 'super_admin',
                'display_name' => 'Super Admin',
                'description' => 'Full access to all features',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'admin',
                'display_name' => 'Admin',
                'description' => 'Administrative access with some restrictions',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'manager',
                'display_name' => 'Manager',
                'description' => 'Can manage leads and view reports',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'viewer',
                'display_name' => 'Viewer',
                'description' => 'Can only view leads',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ];

        DB::table('roles')->insert($roles);

        // Create permissions
        $permissions = [
            // Lead permissions
            [
                'name' => 'leads.view',
                'display_name' => 'View Leads',
                'group' => 'leads',
                'description' => 'Can view leads',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'leads.create',
                'display_name' => 'Create Leads',
                'group' => 'leads',
                'description' => 'Can create new leads',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'leads.edit',
                'display_name' => 'Edit Leads',
                'group' => 'leads',
                'description' => 'Can edit existing leads',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'leads.delete',
                'display_name' => 'Delete Leads',
                'group' => 'leads',
                'description' => 'Can delete leads',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            // Custom field permissions
            [
                'name' => 'custom_fields.view',
                'display_name' => 'View Custom Fields',
                'group' => 'custom_fields',
                'description' => 'Can view custom fields',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'custom_fields.manage',
                'display_name' => 'Manage Custom Fields',
                'group' => 'custom_fields',
                'description' => 'Can create, edit, and delete custom fields',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            // User management permissions
            [
                'name' => 'users.view',
                'display_name' => 'View Users',
                'group' => 'users',
                'description' => 'Can view user list',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'users.manage',
                'display_name' => 'Manage Users',
                'group' => 'users',
                'description' => 'Can create, edit, and delete users',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            // Report permissions
            [
                'name' => 'reports.view',
                'display_name' => 'View Reports',
                'group' => 'reports',
                'description' => 'Can view reports',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'reports.export',
                'display_name' => 'Export Reports',
                'group' => 'reports',
                'description' => 'Can export reports',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ];

        DB::table('permissions')->insert($permissions);

        // Assign permissions to roles
        $superAdminRole = DB::table('roles')->where('name', 'super_admin')->first();
        $adminRole = DB::table('roles')->where('name', 'admin')->first();
        $managerRole = DB::table('roles')->where('name', 'manager')->first();
        $viewerRole = DB::table('roles')->where('name', 'viewer')->first();

        // Get all permissions
        $allPermissions = DB::table('permissions')->get();
        $leadViewPermission = DB::table('permissions')->where('name', 'leads.view')->first();

        // Super Admin gets all permissions
        foreach ($allPermissions as $permission) {
            DB::table('role_permission')->insert([
                'role_id' => $superAdminRole->id,
                'permission_id' => $permission->id,
            ]);
        }

        // Admin gets all except user management
        foreach ($allPermissions as $permission) {
            if ($permission->group !== 'users') {
                DB::table('role_permission')->insert([
                    'role_id' => $adminRole->id,
                    'permission_id' => $permission->id,
                ]);
            }
        }

        // Manager gets lead and report permissions
        foreach ($allPermissions as $permission) {
            if (in_array($permission->group, ['leads', 'reports'])) {
                DB::table('role_permission')->insert([
                    'role_id' => $managerRole->id,
                    'permission_id' => $permission->id,
                ]);
            }
        }

        // Viewer only gets view permissions
        DB::table('role_permission')->insert([
            'role_id' => $viewerRole->id,
            'permission_id' => $leadViewPermission->id,
        ]);
    }
} 