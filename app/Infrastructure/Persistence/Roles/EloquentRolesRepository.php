<?php

namespace App\Infrastructure\Persistence\Roles;

use App\Core\Roles\Contracts\RolesRepository;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Support\Facades\DB; // <-- 1. Import the DB facade

class EloquentRolesRepository implements RolesRepository
{
    public function find(int $id): Role
    {
        return Role::findOrFail($id);
    }

    public function create(array $data): Role
    {
        // 2. Wrap the operation in a database transaction
        return DB::transaction(function () use ($data) {
           
           $role = Role::create([
            'name' => $data['name'],
            'department_id' => $data['department_id'] ?? null,
        ]);

         $permissions = Permission::whereIn('id', $data['permissions'])->get();
            if (!empty($permissions)) {
                $role->givePermissionTo($permissions);
            }

            return $role;
        });
    }

    public function update(int $id, array $data): Role
    {
       
        return DB::transaction(function () use ($id, $data) {
            $role = Role::findOrFail($id);

             $permissions = Permission::whereIn('id', $data['permissions'])->get();

              if (!empty($permissions)) {
                $role->syncPermissions($permissions);
            }
          
            $role->update([
            'name' => $data['name'],
            'department_id' => $data['department_id'] ?? null,
        ]);
            
            return $role;
        });
    }

    public function delete(int $id): void
    {
        // No transaction needed here as it's a single delete operation.
        Role::findOrFail($id)->delete();
    }
}
