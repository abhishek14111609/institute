<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreClassRequest extends FormRequest
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
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $schoolId = $this->user()->school_id;

        return [
            'course_id' => ['nullable', 'exists:courses,id'],
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:academic,sports'],
            'description' => ['nullable', 'string'],
            'is_active' => ['boolean'],
            'subject_template_ids' => ['nullable', 'array'],
            'subject_template_ids.*' => [
                'integer',
                Rule::exists('course_subject_templates', 'id')->where('school_id', $schoolId),
            ],
        ];
    }
}
