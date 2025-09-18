<?php

namespace App\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use App\Models\MasterData;

interface MasterDataRepositoryInterface
{
    public function getAll(array $filters, int $perPage = 10): LengthAwarePaginator;

    public function getActive(): Collection;

    public function findById(int $id): ?MasterData;

    public function create(array $data): MasterData;

    public function update(MasterData $masterData, array $data): MasterData;

    public function delete(int $id): bool;

    public function toggleStatus(MasterData $masterData): MasterData;

    public function bulkDelete(array $ids): int;

    public function bulkToggle(array $ids): int;
}
