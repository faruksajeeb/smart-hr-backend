<?php

namespace App\Repositories;

use App\Models\Employee;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class EmployeeRepository implements EmployeeRepositoryInterface
{
    public function getAll(array $filters, int $perPage = 10): LengthAwarePaginator
    {
        $query = Employee::with('parent');

        if (!empty($filters['search'])) {
            $searchTerm = $filters['search'];
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%")
                    ->orWhere('code', 'like', "%{$searchTerm}%")
                    ->orWhere('type', 'like', "%{$searchTerm}%");
            });
        }

        if (!empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        if (!empty($filters['parent'])) {
            $query->where('parent_id', $filters['parent']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        $sort = $filters['sort'] ?? 'id';
        $direction = $filters['dir'] ?? 'desc';

        $query->orderBy($sort, $direction);

        return $query->paginate($perPage)->withQueryString();
    }

    public function getActive(): Collection
    {
        return Employee::where('status', 1)->get();
    }

    public function findById(int $id): ?Employee
    {
        return Employee::find($id);
    }

    public function create(array $data): Employee
    {
        return Employee::create($data);
    }

    public function update(Employee $employee, array $data): Employee
    {
        $employee->update($data);
        return $employee;
    }

    public function delete(int $id): bool
    {
        return Employee::destroy($id) > 0;
    }

    public function toggleStatus(Employee $employee): Employee
    {
        $employee->status = !$employee->status;
        $employee->save();
        return $employee;
    }


    public function bulkDelete(array $ids): int
    {
        return Employee::whereIn('id', $ids)->delete();
    }

    public function bulkToggle(array $ids): int
    {
        $items = Employee::whereIn('id', $ids)->get();
        foreach ($items as $item) {
            $item->status = !$item->status;
            $item->save();
        }
        return count($items);
    }
}
