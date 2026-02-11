<?php

namespace App\Core\Driver\Services;

use App\Core\Driver\Contracts\DriverRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class DriverService
{
    public function __construct(
        protected DriverRepository $drivers
    ) {}

    public function paginate(int $perPage = 10): LengthAwarePaginator
    {
        return $this->drivers->paginateDrivers($perPage);
    }

    public function get(int $id): User
    {
        return $this->drivers->find($id);
    }
     public function getAll(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->drivers->getAll();
    }
    public function update(int $id, array $data): User
    {
        return $this->drivers->update($id, $data);
    }
    //    ajax toggle without page reload 
    public function toggleActive(int $id): void
    {
        $driver = $this->get($userId);

        $this->update($userId, [
            'active' => ! $driver->active,
        ]);
    }

}
