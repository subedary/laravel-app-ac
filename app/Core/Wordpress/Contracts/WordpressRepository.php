<?php

namespace App\Core\Wordpress\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

interface WordpressRepository
{
    public function paginateWordpress(int $perPage = 10): LengthAwarePaginator;
    public function find(int $id): User;
    public function getAll(): \Illuminate\Database\Eloquent\Collection;
    public function update(int $id, array $data): User;
}


