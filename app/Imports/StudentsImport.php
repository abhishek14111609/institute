<?php

namespace App\Imports;

use App\Models\Batch;
use App\Services\StudentService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class StudentsImport implements ToCollection, WithHeadingRow
{
    private int $importedCount = 0;

    private array $failedRows = [];

    public function __construct(
        private StudentService $studentService,
        private int $schoolId,
    ) {}

    public function collection(Collection $rows): void
    {
        foreach ($rows as $index => $row) {
            $rowNumber = $index + 2;

            $name = trim((string) ($row['name'] ?? ''));
            $email = trim((string) ($row['email'] ?? ''));
            $username = trim((string) ($row['username'] ?? ''));
            $batchName = trim((string) ($row['batch_name'] ?? ''));

            if ($name === '' || $email === '') {
                $this->failedRows[] = [
                    'row' => $rowNumber,
                    'error' => 'Name and email are required.',
                ];
                continue;
            }

            if ($username === '') {
                $username = Str::slug($name, '.') . rand(100, 999);
            }

            $payload = [
                'name' => $name,
                'email' => $email,
                'username' => $username,
                'phone' => trim((string) ($row['phone'] ?? '')) ?: null,
                'roll_number' => trim((string) ($row['roll_number'] ?? '')) ?: null,
                'admission_date' => trim((string) ($row['admission_date'] ?? '')) ?: now()->toDateString(),
                'password' => trim((string) ($row['password'] ?? '')) ?: 'Student@123',
                'parent_name' => trim((string) ($row['parent_name'] ?? '')) ?: null,
                'parent_phone' => trim((string) ($row['parent_phone'] ?? '')) ?: null,
                'address' => trim((string) ($row['address'] ?? '')) ?: null,
            ];

            if ($batchName !== '') {
                $batch = Batch::where('school_id', $this->schoolId)
                    ->where('name', $batchName)
                    ->first();

                if (!$batch) {
                    $this->failedRows[] = [
                        'row' => $rowNumber,
                        'error' => 'Batch not found: ' . $batchName,
                    ];
                    continue;
                }

                $payload['batch_id'] = $batch->id;
            }

            $validator = Validator::make($payload, [
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'email', 'unique:users,email'],
                'username' => ['required', 'string', 'max:100', 'unique:users,username'],
                'password' => ['required', 'string', 'min:8'],
                'admission_date' => ['required', 'date'],
                'batch_id' => ['nullable', 'exists:batches,id'],
            ]);

            if ($validator->fails()) {
                $this->failedRows[] = [
                    'row' => $rowNumber,
                    'error' => $validator->errors()->first(),
                ];
                continue;
            }

            try {
                $this->studentService->createStudent($payload);
                $this->importedCount++;
            } catch (\Throwable $e) {
                $this->failedRows[] = [
                    'row' => $rowNumber,
                    'error' => $e->getMessage(),
                ];
            }
        }
    }

    public function getImportedCount(): int
    {
        return $this->importedCount;
    }

    public function getFailedRows(): array
    {
        return $this->failedRows;
    }
}
