<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class StudentsExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    public function __construct(private Collection $students) {}

    public function collection(): Collection
    {
        return $this->students;
    }

    public function headings(): array
    {
        return [
            'Roll Number',
            'Name',
            'Email',
            'Username',
            'Phone',
            'Batch',
            'Admission Date',
            'Status',
        ];
    }

    public function map($student): array
    {
        $batchNames = $student->batches && $student->batches->isNotEmpty()
            ? $student->batches->pluck('name')->implode(', ')
            : optional($student->batch)->name;

        return [
            $student->roll_number,
            optional($student->user)->name,
            optional($student->user)->email,
            optional($student->user)->username,
            optional($student->user)->phone,
            $batchNames ?: 'N/A',
            $student->admission_date ? $student->admission_date->format('Y-m-d') : '',
            $student->is_active ? 'Active' : 'Inactive',
        ];
    }
}
