<?php

namespace App\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use App\Models\Employee;

interface EmployeeRepositoryInterface
{
    public function getAll(array $filters, int $perPage = 10): LengthAwarePaginator;

    public function getActive(): Collection;

    public function findById(int $id): ?Employee;

    public function create(array $data): Employee;

    public function update(Employee $Employee, array $data): Employee;

    public function delete(int $id): bool;

    public function toggleStatus(Employee $Employee): Employee;

    public function bulkDelete(array $ids): int;

    public function bulkToggle(array $ids): int;
}
