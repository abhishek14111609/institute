<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Batch;
use App\Models\Material;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class MaterialController extends Controller
{
    /**
     * Display all materials uploaded by this teacher.
     */
    public function index(Request $request)
    {
        $teacher = auth()->user()->teacher;
        $school = auth()->user()->school;
        $teacherBatchIds = $teacher ? $teacher->batches()->pluck('batches.id') : collect();

        $materials = Material::where('school_id', $school->id)
            ->where('teacher_id', auth()->id())
            ->when($request->filled('batch_id'), function ($query) use ($request, $teacherBatchIds) {
                $batchId = (int) $request->input('batch_id');

                if ($teacherBatchIds->contains($batchId)) {
                    $query->where('batch_id', $batchId);
                }
            })
            ->when($request->filled('search'), function ($query) use ($request) {
                $query->where('title', 'like', '%' . trim((string) $request->input('search')) . '%');
            })
            ->with('batch')
            ->latest()
            ->paginate(15)
            ->withQueryString();

        // Batches this teacher is assigned to, for folder quick-filter
        $batches = $teacher ? $teacher->batches()->with('class')->get() : collect();

        return view('teacher.materials.index', compact('materials', 'batches'));
    }

    /**
     * Store a newly uploaded file.
     */
    public function store(Request $request)
    {
        $school = auth()->user()->school;

        $request->validate([
            'title' => 'required|string|max:255',
            'file' => 'required|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,png,jpg,jpeg,zip,mp4|max:51200',
            'batch_id' => ['nullable', Rule::exists('batches', 'id')->where('school_id', $school->id)],
        ]);

        if ($request->filled('batch_id')) {
            $teacherBatchIds = auth()->user()->teacher?->batches()->pluck('batches.id') ?? collect();
            abort_unless($teacherBatchIds->contains((int) $request->batch_id), 403);
        }

        $uploaded = $request->file('file');
        $path = $uploaded->store("schools/{$school->id}/materials", 'public');

        Material::create([
            'school_id' => $school->id,
            'teacher_id' => auth()->id(),
            'batch_id' => $request->batch_id,
            'title' => $request->title,
            'file_path' => $path,
            'file_size' => $uploaded->getSize(),
            'file_type' => $uploaded->getClientOriginalExtension(),
        ]);

        return back()->with('success', 'Material uploaded successfully.');
    }

    /**
     * Serve a file download.
     */
    public function download(Material $material)
    {
        // Ensure only teacher who owns it or same school can download
        if ($material->school_id !== auth()->user()->school_id) {
            abort(403);
        }

        $absolutePath = Storage::disk('public')->path($material->file_path);

        return response()->download($absolutePath, $material->title . '.' . $material->file_type);
    }

    /**
     * Delete a material file.
     */
    public function destroy(Material $material)
    {
        if ($material->teacher_id !== auth()->id()) {
            abort(403);
        }

        Storage::disk('public')->delete($material->file_path);
        $material->delete();

        return back()->with('success', 'Material deleted.');
    }
}
