<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\CourseSubjectTemplate;
use App\Models\Classes;
use App\Models\Subject;
use App\Http\Requests\StoreClassRequest;
use Illuminate\Http\Request;

class ClassController extends Controller
{
    public function index()
    {
        $classes = Classes::withCount('batches')
            ->latest()
            ->paginate(15);

        return view('school.classes.index', compact('classes'));
    }

    public function create()
    {
        $courses = \App\Models\Course::active()->get();
        $subjectTemplates = CourseSubjectTemplate::active()
            ->orderBy('name')
            ->get();

        return view('school.classes.create', compact('courses', 'subjectTemplates'));
    }

    public function store(StoreClassRequest $request)
    {
        $data = $request->validated();
        $selectedTemplateIds = collect($data['subject_template_ids'] ?? [])->map(fn($id) => (int) $id)->values();

        unset($data['subject_template_ids']);

        if (empty($data['school_id'])) {
            $data['school_id'] = auth()->user()->school_id;
        }

        $class = Classes::create($data);
        $this->syncTemplateSubjects($class, $selectedTemplateIds);

        return redirect()->route('school.classes.index')
            ->with('success', 'Class created successfully.');
    }

    public function show(Classes $class)
    {
        $class->load([
            'course',
            'batches.students.user',
            'batches.teachers.user'
        ]);

        return view('school.classes.show', compact('class'));
    }

    public function edit(Classes $class)
    {
        $courses = \App\Models\Course::active()->get();
        $subjectTemplates = CourseSubjectTemplate::active()
            ->orderBy('name')
            ->get();

        $assignedTemplateIds = Subject::where('class_id', $class->id)
            ->whereNotNull('subject_template_id')
            ->where('is_active', true)
            ->pluck('subject_template_id')
            ->toArray();

        return view('school.classes.edit', compact('class', 'courses', 'subjectTemplates', 'assignedTemplateIds'));
    }

    public function update(StoreClassRequest $request, Classes $class)
    {
        $data = $request->validated();
        $selectedTemplateIds = collect($data['subject_template_ids'] ?? [])->map(fn($id) => (int) $id)->values();
        unset($data['subject_template_ids']);

        $class->update($data);
        $this->syncTemplateSubjects($class, $selectedTemplateIds);

        return redirect()->route('school.classes.index')
            ->with('success', 'Class updated successfully.');
    }

    public function destroy(Classes $class)
    {
        $class->delete();

        return redirect()->route('school.classes.index')
            ->with('success', 'Class deleted successfully.');
    }

    public function toggleStatus(Classes $class)
    {
        $class->update([
            'is_active' => !$class->is_active
        ]);

        return back()->with('success', 'Class status updated successfully.');
    }

    /**
     * Create or update class-level subjects from selected course templates.
     */
    private function syncTemplateSubjects(Classes $class, $selectedTemplateIds): void
    {
        if (empty($class->course_id)) {
            Subject::where('class_id', $class->id)
                ->whereNotNull('subject_template_id')
                ->update(['is_active' => false]);

            return;
        }

        $allowedTemplateIds = CourseSubjectTemplate::query()
            ->where('course_id', $class->course_id)
            ->whereIn('id', $selectedTemplateIds)
            ->pluck('id');

        // Deactivate deselected template subjects but keep history-safe records.
        Subject::where('class_id', $class->id)
            ->whereNotNull('subject_template_id')
            ->whereNotIn('subject_template_id', $allowedTemplateIds)
            ->update(['is_active' => false]);

        if ($allowedTemplateIds->isEmpty()) {
            return;
        }

        $templates = CourseSubjectTemplate::whereIn('id', $allowedTemplateIds)->get();

        foreach ($templates as $template) {
            $subject = Subject::withTrashed()->firstOrNew([
                'school_id' => $class->school_id,
                'class_id' => $class->id,
                'subject_template_id' => $template->id,
            ]);

            if ($subject->exists && $subject->trashed()) {
                $subject->restore();
            }

            $subject->fill([
                'level_id' => null,
                'name' => $template->name,
                'activity_name' => null,
                'type' => 'academic',
                'description' => $template->description,
                'is_active' => true,
            ]);

            $subject->save();
        }
    }
}
