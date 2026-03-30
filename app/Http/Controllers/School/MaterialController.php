<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\Batch;
use App\Models\Material;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class MaterialController extends Controller
{
    /**
     * Display all materials for this school.
     */
    public function index()
    {
        $school = auth()->user()->school;

        $materials = Material::where('school_id', $school->id)
            ->with(['batch', 'teacher'])
            ->latest()
            ->paginate(15);

        // Required for the creation form
        $batches = Batch::where('school_id', $school->id)->with('class')->get();
        
        // Teachers to assign if admin uploads (default to current admin user if no teacher selected)
        $teachers = User::role('teacher')->where('school_id', $school->id)->get();

        return view('school.materials.index', compact('materials', 'batches', 'teachers'));
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
            'teacher_id' => ['nullable', Rule::exists('users', 'id')->where('school_id', $school->id)],
        ]);

        $uploaded = $request->file('file');
        $path = $uploaded->store("schools/{$school->id}/materials", 'public');

        Material::create([
            'school_id' => $school->id,
            'teacher_id' => $request->teacher_id ?? auth()->id(), // Admin can upload as self or asst teacher
            'batch_id' => $request->batch_id,
            'title' => $request->title,
            'file_path' => $path,
            'file_size' => $uploaded->getSize(),
            'file_type' => $uploaded->getClientOriginalExtension(),
        ]);

        return back()->with('success', 'Material added successfully.');
    }

    /**
     * Serve a file download.
     */
    public function download(Material $material)
    {
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
        if ($material->school_id !== auth()->user()->school_id) {
            abort(403);
        }

        Storage::disk('public')->delete($material->file_path);
        $material->delete();

        return back()->with('success', 'Material removed.');
    }
}
