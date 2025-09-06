<?php

namespace App\Imports;

use App\Models\MasterData;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;

use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;

class MasterDataImport implements ToModel, WithHeadingRow, WithValidation, SkipsEmptyRows
{
    private int $rowCount = 0;
    protected static array $seenTypeName = [];
    public array $rows = [];

    public function model(array $row)
    {
        $this->rowCount++;


        $key = strtolower(trim($row['type']) . '|' . trim($row['name']));

        // 🔴 Check duplicate inside file
        if (in_array($key, self::$seenTypeName)) {
            $validator = Validator::make([], []); // empty validator
            $validator->errors()->add('name', "Duplicate Type + Name '{$row['type']} - {$row['name']}' found in the file.");

            throw new ValidationException($validator);
        }

        self::$seenTypeName[] = $key;

        // 🔴 Check duplicate against DB
        if (MasterData::where('type', $row['type'])->where('name', $row['name'])->exists()) {
            $validator = Validator::make([], []);
            $validator->errors()->add('name', "The combination '{$row['type']} - {$row['name']}' already exists in database.");

            throw new ValidationException($validator);
        }

        // 🔹 Check parent
        $parent_id = null;
        if (!empty($row['parent'])) {
            $parent = MasterData::where('name', $row['parent'])->first();
            if ($parent) {
                $parent_id = $parent->id;
            } else {
                // Optional: throw error if parent does not exist
                $validator = Validator::make([], []);
                $validator->errors()->add('parent', "Parent '{$row['parent']}' does not exist in database.");
                throw new ValidationException($validator);
            }
        }


        return new MasterData([
            'type' => $row['type'] ?? null,
            'name' => $row['name'] ?? null,
            'code' => $row['code'] ?? null,
            'parent_id'  => $parent_id,
            'description' => $row['description'] ?? null,
            'status' => $row['status'] ?? 1,
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

            '*.name.unique' => 'Name already exists in the system.',
            '*.code.unique' => 'Code already exists in the system.',
            '*.name.distinct' => 'Duplicate name found in the file.',
            '*.code.distinct' => 'Duplicate code found in the file.',

        ];
    }

    public function getRowCount(): int
    {
        return $this->rowCount;
    }
}
