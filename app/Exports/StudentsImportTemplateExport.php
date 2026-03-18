<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class StudentsImportTemplateExport implements FromArray, WithHeadings, ShouldAutoSize
{
    public function headings(): array
    {
        return [
            'name',
            'email',
            'username',
            'phone',
            'roll_number',
            'batch_name',
            'admission_date',
            'password',
            'parent_name',
            'parent_phone',
            'address',
        ];
    }

    public function array(): array
    {
        return [
            [
                'Rohit Sharma',
                'rohit@example.com',
                'rohit.sharma',
                '9876543210',
                'PAN001',
                'Morning Cricket Batch',
                now()->format('Y-m-d'),
                'Student@123',
                'Rajesh Sharma',
                '9876500000',
                'Pune',
            ],
        ];
    }
}
