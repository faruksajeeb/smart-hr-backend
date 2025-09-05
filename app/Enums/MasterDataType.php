<?php
namespace App\Enums;
enum MasterDataType: string {
    case COMPANY = 'company';
    case BRANCH = 'branch';
    case DIVISION = 'division';
    case DEPARTMENT = 'department';
    case DESIGNATION = 'designation';
    case BLOOD_GROUP = 'blood_group';
    case GRADE = 'grade';
    case BANK = 'bank';
    case RELIGION = 'religion';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}