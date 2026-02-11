<?php

namespace App\Core\Driver\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

interface DriverRepository
{
    public function paginateDrivers(int $perPage = 10): LengthAwarePaginator;
    public function find(int $id): User;
    public function getAll(): \Illuminate\Database\Eloquent\Collection;
    public function update(int $id, array $data): User;
}
