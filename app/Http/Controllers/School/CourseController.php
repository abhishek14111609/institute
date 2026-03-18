<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Classes;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CourseController extends Controller
{
    private function makeSchoolCodePrefix(?string $schoolName): string
    {
        $prefix = strtoupper(substr(preg_replace('/[^A-Za-z]/', '', (string) $schoolName), 0, 3));
        return str_pad($prefix ?: 'INS', 3, 'X');
    }

    private function nextSchoolCourseSequence(int $schoolId, string $prefix): int
    {
        $max = 0;

        Course::withTrashed()
            ->where('school_id', $schoolId)
            ->where('code', 'like', $prefix . '%')
            ->pluck('code')
            ->each(function ($code) use (&$max, $prefix) {
                if (preg_match('/^' . preg_quote($prefix, '/') . '(\d+)$/', (string) $code, $m)) {
                    $max = max($max, (int) $m[1]);
                }
            });

        return $max + 1;
    }

    private function buildSchoolSequentialCode(string $prefix, int $sequence): string
    {
        return sprintf('%s%03d', $prefix, $sequence);
    }

    private function generateSchoolSequentialCode(int $schoolId, ?string $schoolName): string
    {
        $prefix = $this->makeSchoolCodePrefix($schoolName);
        $nextSequence = $this->nextSchoolCourseSequence($schoolId, $prefix);
        return $this->buildSchoolSequentialCode($prefix, $nextSequence);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $courses = Course::withCount('classes')->latest()->paginate(10);
        return view('school.courses.index', compact('courses'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $school = auth()->user()->school;
        $schoolId = auth()->user()->school_id;
        $prefix = $this->makeSchoolCodePrefix($school->name ?? null);
        $nextSequence = $this->nextSchoolCourseSequence($schoolId, $prefix);
        $suggestedCode = $this->buildSchoolSequentialCode($prefix, $nextSequence);

        return view('school.courses.create', compact('prefix', 'nextSequence', 'suggestedCode'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $schoolId = auth()->user()->school_id;

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('courses')->where('school_id', $schoolId),
            ],
            'code' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ], [
            'name.unique' => 'A course with this name already exists in your school.',
        ]);

        $validated['school_id'] = $schoolId;

        if (auth()->user()->school->institute_type === 'sport') {
            $validated['code'] = $this->generateSchoolSequentialCode($schoolId, auth()->user()->school->name ?? null);
        } elseif (empty($validated['code'])) {
            $validated['code'] = $this->generateSchoolSequentialCode($schoolId, auth()->user()->school->name ?? null);
        }

        $course = Course::create($validated);

        // For sports academies, auto-create a hidden "Default Team" for this program
        if (auth()->user()->school->institute_type === 'sport') {
            Classes::create([
                'school_id' => $schoolId,
                'course_id' => $course->id,
                'name' => $course->name . ' - Default Team',
                'is_active' => true
            ]);
        }

        return redirect()->route('school.courses.index')
            ->with('success', 'Program created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Course $course)
    {
        $course->load('classes.batches');
        return view('school.courses.show', compact('course'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Course $course)
    {
        $school = auth()->user()->school;
        $schoolId = auth()->user()->school_id;
        $prefix = $this->makeSchoolCodePrefix($school->name ?? null);
        $nextSequence = $this->nextSchoolCourseSequence($schoolId, $prefix);
        $suggestedCode = $this->buildSchoolSequentialCode($prefix, $nextSequence);

        return view('school.courses.edit', compact('course', 'prefix', 'nextSequence', 'suggestedCode'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Course $course)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $course->update($validated);

        // Sync shadow-class name if it exists for sports akademies
        if (auth()->user()->school->institute_type === 'sport') {
            $course->classes()->update(['name' => $course->name . ' - Default Team']);
        }

        return redirect()->route('school.courses.index')
            ->with('success', 'Program updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Course $course)
    {
        $course->delete();

        return redirect()->route('school.courses.index')
            ->with('success', 'Course deleted successfully.');
    }
}
