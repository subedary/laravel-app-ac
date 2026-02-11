<?php
namespace App\Infrastructure\Persistence\Permissions;

use App\Core\Permissions\Contracts\PermissionsRepository;
use App\Models\Module;
use App\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Str;

class EloquentPermissionsRepository implements PermissionsRepository
{
    public function find(int $id): Permission
    {
        return Permission::findOrFail($id);
    }

    public function create(array $data): Permission
    {
         $data['slug'] = $data['slug'] ?? Str::slug($data['name']);
         $data['web'] = $data['web'] ?? 'web';

           $permission = Permission::create([
            'name' => $data['name'],
            'display_name' => $data['display_name'],
            'module_id'     => $data['module_id'],
            'slug'          => $data['slug'],
            'guard_name'    => $data['web'],
        ]);

      
        return $permission;
    }

    public function update(int $id, array $data): Permission
    {
         $data['slug'] = $data['slug'] ?? Str::slug($data['name']);
          $data['web'] = $data['web'] ?? 'web';
       
        $Permission = Permission::findOrFail($id);
        $Permission->update($data);
        return $Permission;
    }

    public function delete(int $id): void
    {
        Permission::findOrFail($id)->delete();
    }
}
