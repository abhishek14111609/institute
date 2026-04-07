<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseSubjectTemplate;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CourseSubjectTemplateController extends Controller
{
    public function index()
    {
        $templates = CourseSubjectTemplate::with('course')
            ->withCount('subjects')
            ->latest()
            ->paginate(20);

        return view('school.subject-templates.index', compact('templates'));
    }

    public function create()
    {
        $courses = Course::active()->orderBy('name')->get();

        return view('school.subject-templates.create', compact('courses'));
    }

    public function store(Request $request)
    {
        $schoolId = auth()->user()->school_id;

        $validated = $request->validate([
            'course_id' => ['required', Rule::exists('courses', 'id')->where('school_id', $schoolId)],
            'subject_names' => ['nullable', 'array'],
            'subject_names.*' => ['nullable', 'string', 'max:255'],
            'names' => ['nullable', 'string'],
            'description' => ['nullable', 'string'],
        ]);

        $names = collect($validated['subject_names'] ?? [])
            ->merge(
                filled($validated['names'] ?? null)
                    ? preg_split('/\r\n|\r|\n|,/', $validated['names'])
                    : []
            )
            ->map(fn($name) => trim((string) $name))
            ->filter()
            ->unique(fn($name) => mb_strtolower($name))
            ->values();

        if ($names->isEmpty()) {
            return back()
                ->withErrors(['subject_names' => 'Please add at least one subject.'])
                ->withInput();
        }

        foreach ($names as $name) {
            CourseSubjectTemplate::firstOrCreate(
                [
                    'school_id' => $schoolId,
                    'course_id' => $validated['course_id'],
                    'name' => $name,
                ],
                [
                    'description' => $validated['description'] ?? null,
                    'is_active' => true,
                ]
            );
        }

        return redirect()->route('school.subject-templates.index')
            ->with('success', 'Course-wise subjects saved successfully.');
    }

    public function edit(CourseSubjectTemplate $subjectTemplate)
    {
        $courses = Course::active()->orderBy('name')->get();

        return view('school.subject-templates.edit', compact('subjectTemplate', 'courses'));
    }

    public function update(Request $request, CourseSubjectTemplate $subjectTemplate)
    {
        $schoolId = auth()->user()->school_id;

        $validated = $request->validate([
            'course_id' => ['required', Rule::exists('courses', 'id')->where('school_id', $schoolId)],
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('course_subject_templates', 'name')
                    ->where(function ($query) use ($schoolId, $request) {
                        return $query->where('school_id', $schoolId)
                            ->where('course_id', $request->input('course_id'))
                            ->whereNull('deleted_at');
                    })
                    ->ignore($subjectTemplate->id),
            ],
            'description' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $subjectTemplate->update([
            'course_id' => $validated['course_id'],
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->route('school.subject-templates.index')
            ->with('success', 'Course-wise subject updated successfully.');
    }

    public function destroy(CourseSubjectTemplate $subjectTemplate)
    {
        $subjectTemplate->delete();

        return back()->with('success', 'Course-wise subject deleted successfully.');
    }
}
