<?php

namespace App\Infrastructure\Persistence\Driver;

use App\Core\Driver\Contracts\DriverRepository;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class EloquentDriverRepository implements DriverRepository
{
    public function paginateDrivers(int $perPage = 10): LengthAwarePaginator
    {
        return User::where('driver', true)
            ->latest()
            ->paginate($perPage);
    }

    public function find(int $id): User
    {
        return User::findOrFail($id);
    }
    public function update(int $id, array $data): User
    {
        $user = $this->find($id);
        $user->update($data);
        return $user;
    }
//     public function getAll(): \Illuminate\Database\Eloquent\Collection
//     {
//         // return Driver::all();
//          return User::where('driver', true)::all();
//     }
    public function getAll(): \Illuminate\Database\Eloquent\Collection
    {
        return User::with(['roles', 'department', 'publications', 'status'])
            ->where('driver', true)
            ->latest()
            ->get();
    }
}
