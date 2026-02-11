<?php

namespace App\Infrastructure\Persistence\Wordpress;

use App\Core\Wordpress\Contracts\WordpressRepository;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class EloquentWordpressRepository implements WordpressRepository
{
    
    public function getAll(): Collection
    {
        return User::query()
            ->where('is_wordpress_user', true)
            ->with([
                'roles',
                'department',
                'publications',
                'status'
            ])
            ->latest()
            ->get();
    }

  
    public function find(int $id): User
    {
        return User::where('is_wordpress_user', true)
            ->with([
                'roles',
                'department',
                'publications',
                'status'
            ])
            ->findOrFail($id);
    }

    
    public function update(int $id, array $data): User
    {
        $user = $this->find($id);

        // Extract relations
        $roles = $data['roles'] ?? null;
        $publications = $data['publications'] ?? null;

        unset($data['roles'], $data['publications']);

        // Update main user fields
        $user->update($data);

        // Sync roles (if provided)
        if (is_array($roles)) {
            $user->syncRoles($roles);
        }

        // Sync publications (if provided)
        if (is_array($publications)) {
            $user->publications()->sync($publications);
        }

        return $user;
    }

    
    public function toggleActive(int $id): User
    {
        $user = $this->find($id);

        $user->update([
            'active' => ! $user->active
        ]);

        return $user;
    }
}
