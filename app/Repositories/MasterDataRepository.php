<?php

namespace App\Repositories;

use App\Models\MasterData;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class MasterDataRepository implements MasterDataRepositoryInterface
{
    public function getAll(array $filters, int $perPage = 10): LengthAwarePaginator
    {
        $query = MasterData::with('parent');

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
        return MasterData::where('status', 1)->get();
    }

    public function findById(int $id): ?MasterData
    {
        return MasterData::find($id);
    }

    public function create(array $data): MasterData
    {
        return MasterData::create($data);
    }

    public function update(MasterData $masterData, array $data): MasterData
    {
        $masterData->update($data);
        return $masterData;
    }

    public function delete(int $id): bool
    {
        return MasterData::destroy($id) > 0;
    }

    public function toggleStatus(MasterData $masterData): MasterData
    {
        $masterData->status = !$masterData->status;
        $masterData->save();
        return $masterData;
    }


    public function bulkDelete(array $ids): int
    {
        return MasterData::whereIn('id', $ids)->delete();
    }

    public function bulkToggle(array $ids): int
    {
        $items = MasterData::whereIn('id', $ids)->get();
        foreach ($items as $item) {
            $item->status = !$item->status;
            $item->save();
        }
        return count($items);
    }
}
