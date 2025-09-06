<?php
namespace App\Exports;

use App\Models\MasterData;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class MasterDataExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        return MasterData::with('parent')->get();
    }

    // 🔹 Add headings for Excel
    public function headings(): array
    {
        return [
            'Type',
            'Name',
            'Code',
            'Parent',
            'Description',
            'Status',
        ];
    }

    // 🔹 Map each row
    public function map($row): array
    {
        return [
            $row->type,
            $row->name,
            $row->code,
            $row->parent ? $row->parent->name : null,
            $row->description,
            $row->status ? 'Active' : 'Inactive',
        ];
    }
}
