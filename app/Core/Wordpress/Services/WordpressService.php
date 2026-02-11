<?php

namespace App\Core\Wordpress\Services;

use App\Core\Driver\Contracts\WordpressRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
class WordpressService
{
    public function getAll()
    {
        return User::where('is_wordpress_user', true)
            ->with(['roles', 'department', 'publications', 'status'])
            ->latest()
            ->get();
    }

    public function toggleActive(int $id)
    {
        $user = User::where('is_wordpress_user', true)->findOrFail($id);

        $user->update([
            'active' => ! $user->active
        ]);

        return $user;
    }
}
