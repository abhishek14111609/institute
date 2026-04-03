<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreBatchRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->isSchoolAdmin();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $isSport = auth()->user()->school->institute_type === 'sport';
        $schoolId = auth()->user()->school_id;
        $subjectId = $this->input('subject_id');
        $classId = $this->input('class_id');

        $batchRouteParam = $this->route('batch');
        $batchId = is_object($batchRouteParam) ? $batchRouteParam->id : $batchRouteParam;
        $uniqueNameRule = Rule::unique('batches', 'name')
            ->ignore($batchId)
            ->where(function ($query) use ($schoolId, $subjectId, $classId) {
                return $query
                    ->where('school_id', $schoolId)
                    ->where('subject_id', $subjectId)
                    ->where('class_id', $classId)
                    ->whereNull('deleted_at');
            });

        return [
            'class_id' => [$isSport ? 'nullable' : 'required', 'exists:classes,id'],
            'course_id' => [$isSport ? 'required' : 'nullable', 'exists:courses,id'],
            'subject_id' => ['required', 'exists:subjects,id'],
            'name' => [$isSport ? 'nullable' : 'required', 'string', 'max:255', $uniqueNameRule],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
            'capacity' => ['required', 'integer', 'min:1', 'max:10000'],
            'sport_level' => ['nullable', 'string', 'max:255'],
            'is_active' => ['boolean'],
            'teacher_ids' => ['nullable', 'array'],
            'teacher_ids.*' => ['exists:teachers,id'],
            'student_ids' => ['nullable', 'array'],
            'student_ids.*' => ['exists:students,id'],
        ];
    }

    /**
     * Custom validation messages.
     */
    public function messages(): array
    {
        return [
            'name.unique' => 'A batch with this name already exists for this subject and class.',
        ];
    }
}
