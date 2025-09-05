<?php

namespace App\Imports;

use App\Models\MasterData;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;


class MasterDataImport implements ToModel, WithHeadingRow, WithValidation, SkipsEmptyRows
{
    private int $rowCount = 0;

    public function model(array $row)
    {
        $this->rowCount++;

        return new MasterData([
            'type'        => $row['type'] ?? null,
            'name'        => $row['name'] ?? null,
            'code'        => $row['code'] ?? null,
            'parent_id'   => $row['parent_id'] ?? null,
            'description' => $row['description'] ?? null,
            'status'      => $row['status'] ?? 1,
        ]);
    }

     public function rules(): array
    {
        return [
            '*.type' => ['required', 'string', 'max:50'],
            '*.name' => ['required', 'string', 'max:100'],
            '*.code' => ['required', 'string', 'max:50'],
            '*.status' => ['nullable', 'boolean'],
        ];
    }

    public function customValidationMessages()
    {
        return [
            '*.type.required' => 'Type is required in every row.',
            '*.name.required' => 'Name is required in every row.',
            '*.code.required' => 'Code is required in every row.',
            '*.status.boolean' => 'Status must be 0 or 1.',
        ];
    }

    public function getRowCount(): int
    {
        return $this->rowCount;
    }
}
