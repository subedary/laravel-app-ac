<?php

// database/seeders/ModuleAndPermissionSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Module;
use App\Models\Permission;
use Illuminate\Support\Facades\DB; // Optional, for a clean reset

class ModuleAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Optional: Clear existing data to avoid duplicates on re-seeding
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Permission::truncate();
        Module::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

         // Define all your modules and permissions in one place
        $modulesAndPermissions = [
            'User Management' => [
                'permissions' => [
                    ['name' => 'create-user', 'display_name' => 'Create User','slug' => 'create-user'],
                    ['name' => 'edit-user', 'display_name' => 'Edit User','slug' => 'edit-user'],
                    ['name' => 'delete-user', 'display_name' => 'Delete User','slug' => 'delete-user'],
                    ['name' => 'list-users', 'display_name' => 'View Users','slug' => 'list-users'],
                ],
                'slug' => 'user-management'
            ],
             'Roles Management' => [
                'permissions' => [
                    ['name' => 'create-role', 'display_name' => 'Create Role','slug' => 'create-role'],
                    ['name' => 'edit-role', 'display_name' => 'Edit Role','slug' => 'edit-role'],
                    ['name' => 'delete-role', 'display_name' => 'Delete Role','slug' => 'delete-role'],
                    ['name' => 'list-role', 'display_name' => 'View Roles','slug' => 'list-role'],
                ],
                'slug' => 'roles-management'
            ],

            'Permission Management' => [
                'permissions' => [
                    ['name' => 'create-permission', 'display_name' => 'Create Permission','slug' => 'create-permission'],
                    ['name' => 'edit-permission', 'display_name' => 'Edit Permission','slug' => 'edit-permission'],
                    ['name' => 'delete-permission', 'display_name' => 'Delete Permission','slug' => 'delete-permission'],
                    ['name' => 'list-permission', 'display_name' => 'View Permission','slug' => 'list-permission'],
                ],
                'slug' => 'permission-management'
            ],

            'Modules Management' => [
                'permissions' => [
                    ['name' => 'create-modules', 'display_name' => 'Create Modules','slug' => 'create-modules'],
                    ['name' => 'edit-modules', 'display_name' => 'Edit Modules','slug' => 'edit-modules'],
                    ['name' => 'delete-modules', 'display_name' => 'Delete Modules','slug' => 'delete-modules'],
                    ['name' => 'list-modules', 'display_name' => 'View Modules','slug' => 'list-modules'],
                ],
                'slug' => 'modules-management'
            ],
            'Client Management' => [
                'permissions' => [
                    ['name' => 'create-client', 'display_name' => 'Create Client','slug' => 'create-client'],
                    ['name' => 'edit-client', 'display_name' => 'Edit Client','slug' => 'edit-client'],
                    ['name' => 'delete-client', 'display_name' => 'Delete Client','slug' => 'delete-client'],
                    ['name' => 'list-client', 'display_name' => 'View Client','slug' => 'list-client'],
                ],
                'slug' => 'client-management'
            ],
            'Driver Management' => [
                'permissions' => [
                    ['name' => 'create-driver', 'display_name' => 'Create driver','slug' => 'create-driver'],
                    ['name' => 'edit-driver', 'display_name' => 'Edit driver','slug' => 'edit-driver'],
                    ['name' => 'delete-driver', 'display_name' => 'Delete driver','slug' => 'delete-driver'],
                    ['name' => 'list-driver', 'display_name' => 'View driver','slug' => 'list-driver'],
                ],
                'slug' => 'driver-management'
            ],
             'Wordpress User Management' => [
                'permissions' => [
                    ['name' => 'create-wordpress-user', 'display_name' => 'Create Wordpress User','slug' => 'create-wordpress-user'],
                    ['name' => 'edit-wordpress-user', 'display_name' => 'Edit Wordpress User','slug' => 'edit-wordpress-user'],
                    ['name' => 'delete-wordpress-user', 'display_name' => 'Delete Wordpress User','slug' => 'delete-wordpress-user'],
                    ['name' => 'list-wordpress-user', 'display_name' => 'View Wordpress User','slug' => 'list-wordpress-user'],
                ],
                'slug' => 'wordpress-user--management'
            ],
            'Vehicle Management' => [
                'permissions' => [
                    ['name' => 'create-vehicle', 'display_name' => 'Create vehicle','slug' => 'create-vehicle'],
                    ['name' => 'edit-vehicle', 'display_name' => 'Edit vehicle','slug' => 'edit-vehicle'],
                    ['name' => 'delete-vehicle', 'display_name' => 'Delete vehicle','slug' => 'delete-vehicle'],
                    ['name' => 'list-vehicle', 'display_name' => 'View vehicle','slug' => 'list-vehicle'],
                ],
                'slug' => 'vehicle-management'
            ],

             'Timesheet Management' => [
                'permissions' => [
                    ['name' => 'create-timesheet', 'display_name' => 'Create Timesheet','slug' => 'create-timesheet'],
                    ['name' => 'edit-timesheet', 'display_name' => 'Edit Timesheet','slug' => 'edit-timesheet'],
                    ['name' => 'delete-timesheet', 'display_name' => 'Delete Timesheet','slug' => 'delete-timesheet'],
                    ['name' => 'list-timesheets', 'display_name' => 'View Timesheet','slug' => 'list-timesheets'],
                ],
                'slug' => 'timesheet-management'

            ],
            'Time Off Request Management' => [
                'permissions' => [
                    ['name' => 'create-time-off-request', 'display_name' => 'Create Time Off Request', 'slug' => 'create-time-off-request'],
                    ['name' => 'edit-time-off-request', 'display_name' => 'Edit Time Off Request', 'slug' => 'edit-time-off-request'],
                    ['name' => 'delete-time-off-request', 'display_name' => 'Delete Time Off Request', 'slug' => 'delete-time-off-request'],
                    ['name' => 'list-time-off-requests', 'display_name' => 'List Time Off Requests', 'slug' => 'list-time-off-requests'],
                    ['name' => 'status-time-off-request', 'display_name' => 'Change Status Time Off Request', 'slug' => 'status-time-off-request'],
                    ['name' => 'admin-time-off-requests', 'display_name' => 'Admin Time Off Request', 'slug' => 'admin-time-off-requests'],
                ],
                'slug' => 'time-off-request-management'
            ],
            'Droppoint Management' => [
                'permissions' => [
                    ['name' => 'create-dropoint', 'display_name' => 'Create dropoint','slug' => 'create-dropoint'],
                    ['name' => 'edit-dropoint', 'display_name' => 'Edit dropoint','slug' => 'edit-dropoint'],
                    ['name' => 'delete-dropoint', 'display_name' => 'Delete dropoint','slug' => 'delete-dropoint'],
                    ['name' => 'list-dropoint', 'display_name' => 'View dropoint','slug' => 'list-dropoint'],
                ],
                'slug' => 'dropoint-management'
            ],
            'Contact Management' => [
                'permissions' => [
                    ['name' => 'create-contact', 'display_name' => 'Create contact','slug' => 'create-contact'],
                    ['name' => 'edit-contact', 'display_name' => 'Edit contact','slug' => 'edit-contact'],
                    ['name' => 'delete-contact', 'display_name' => 'Delete contact','slug' => 'delete-contact'],
                    ['name' => 'list-contact', 'display_name' => 'View contact','slug' => 'list-contact'],
                    ['name' => 'list-contact-item', 'display_name' => 'View contact Items','slug' => 'list-contact-item'],
                    ['name' => 'create-contact-item', 'display_name' => 'Create contact item','slug' => 'create-contact-item'],
                    ['name' => 'edit-contact-item', 'display_name' => 'Edit contact item','slug' => 'edit-contact-item'],
                    ['name' => 'delete-contact-item', 'display_name' => 'Delete contact item','slug' => 'delete-contact-item'],
                ],
                'slug' => 'contact-management'
            ]
            ];
 

        $guardName = 'web';

        foreach ($modulesAndPermissions as $moduleName => $data) {
            // Use firstOrCreate to avoid creating duplicates.
            // It will find the module by its unique slug, or create it if it doesn't exist.
            $module = Module::firstOrCreate(
                ['slug' => $data['slug']],
                ['name' => $moduleName]
            );

            foreach ($data['permissions'] as $permissionData) {
                // Use firstOrCreate for permissions as well.
                // This prevents errors if you re-run the seeder.
                Permission::firstOrCreate(
                    // Find by the unique name
                    ['name' => $permissionData['name'], 'guard_name' => $guardName],
                    // Create with these details if not found
                    [
                        'display_name' => $permissionData['display_name'],
                        'module_id' => $module->id,
                        'guard_name' => $guardName,
                        'slug' => $permissionData['slug'],
                    ]
                );
            }
        }

    }
}