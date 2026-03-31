<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Material;
use Illuminate\Support\Facades\Storage;

class MaterialController extends Controller
{
    public function download(Material $material)
    {
        $student = auth()->user()->student;
        $activeBatchIds = $student->batches()
            ->wherePivot('is_active', true)
            ->pluck('batches.id')
            ->push($student->batch_id)
            ->filter()
            ->unique()
            ->values();

        abort_unless(
            $material->school_id === $student->school_id
            && ($material->batch_id === null || $activeBatchIds->contains($material->batch_id)),
            403
        );

        $absolutePath = Storage::disk('public')->path($material->file_path);

        return response()->download($absolutePath, $material->title . '.' . $material->file_type);
    }
}
