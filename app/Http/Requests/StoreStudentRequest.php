<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreStudentRequest extends FormRequest
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
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'username' => ['required', 'string', 'max:100', 'unique:users,username'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'phone' => ['nullable', 'string', 'size:10'],
            'course_id' => ['nullable', 'exists:courses,id'],
            'batch_id' => ['nullable', 'exists:batches,id'],
            'batch_ids' => ['nullable', 'array'],
            'batch_ids.*' => ['distinct', 'exists:batches,id'],
            'batch_fees' => ['nullable', 'array'],
            'fee_plan_id' => ['nullable', 'exists:fee_plans,id'],
            'roll_number' => [
                'nullable',
                'string',
                'max:50',
                Rule::unique('students', 'roll_number')->where('school_id', $this->user()->school_id),
            ],
            'birth_date' => ['nullable', 'date', 'before:today'],
            'previous_school' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string'],
            'parent_name' => ['nullable', 'string', 'max:255'],
            'parent_phone' => ['nullable', 'string', 'size:10'],
            'photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
            'admission_date' => ['required', 'date', 'before_or_equal:today', 'after_or_equal:birth_date'],
        ];
    }
}
