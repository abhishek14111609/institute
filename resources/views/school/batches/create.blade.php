@extends('layouts.app')

@section('title', auth()->user()->school->institute_type === 'sport' ? 'Create Training Batch' : 'Create Batch')

@section('sidebar')
    @include('school.sidebar')
@endsection

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>{{ $isSport ? 'Create New Training Batch' : 'Create New Batch' }}</h2>
            <a href="{{ route('school.batches.index') }}" class="btn btn-outline-secondary rounded-pill px-4">
                <i class="bi bi-arrow-left"></i> Back to List
            </a>
        </div>

        <div class="card">
            <div class="card-body">
                <form action="{{ route('school.batches.store') }}" method="POST">
                    @csrf

                    <div class="row">
                        @if ($isSport)
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="course_id" class="form-label">Sport (Program) <span
                                            class="text-danger">*</span></label>
                                    <select class="form-select @error('course_id') is-invalid @enderror" id="course_id"
                                        name="course_id" required>
                                        <option value="">Select Sport</option>
                                        @foreach ($courses as $course)
                                            <option value="{{ $course->id }}"
                                                {{ old('course_id') == $course->id ? 'selected' : '' }}>
                                                {{ $course->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('course_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="subject_id" class="form-label">Batch Type (Activity & Level) <span
                                            class="text-danger">*</span></label>
                                    <select class="form-select @error('subject_id') is-invalid @enderror" id="subject_id"
                                        name="subject_id" required>
                                        <option value="">Select Batch Type</option>
                                        @foreach ($subjects as $subject)
                                            <option value="{{ $subject->id }}"
                                                data-course="{{ $subject->schoolClass->course_id ?? '' }}"
                                                {{ old('subject_id') == $subject->id ? 'selected' : '' }}>
                                                {{ $subject->name }} ({{ $subject->level->name ?? 'N/A' }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('subject_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Batch Name <small
                                            class="text-muted">(Optional)</small></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                                        id="name" name="name" value="{{ old('name') }}"
                                        placeholder="Auto-generated if empty">
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        @else
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="course_id" class="form-label">Course <span
                                            class="text-danger">*</span></label>
                                    <select class="form-select @error('course_id') is-invalid @enderror" id="course_id"
                                        name="course_id" required>
                                        <option value="">Select Course</option>
                                        @foreach ($courses as $course)
                                            <option value="{{ $course->id }}"
                                                {{ old('course_id') == $course->id ? 'selected' : '' }}>
                                                {{ $course->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('course_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="class_id" class="form-label">Class <span
                                            class="text-danger">*</span></label>
                                    <select class="form-select @error('class_id') is-invalid @enderror" id="class_id"
                                        name="class_id" required>
                                        <option value="">Select Class</option>
                                        @foreach ($classes as $class)
                                            <option value="{{ $class->id }}"
                                                data-course="{{ $class->course_id ?? '' }}"
                                                {{ old('class_id') == $class->id ? 'selected' : '' }}>
                                                {{ $class->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('class_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="subject_id" class="form-label">Subject <span
                                            class="text-danger">*</span></label>
                                    <select class="form-select @error('subject_id') is-invalid @enderror" id="subject_id"
                                        name="subject_id" required>
                                        <option value="">Select Subject</option>
                                        @foreach ($subjects as $subject)
                                            <option value="{{ $subject->id }}"
                                                data-class="{{ $subject->class_id }}"
                                                {{ old('subject_id') == $subject->id ? 'selected' : '' }}>
                                                {{ $subject->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('subject_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Batch Name <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                                        id="name" name="name" value="{{ old('name') }}"
                                        placeholder="e.g. Section A" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="start_time" class="form-label">Start Time <span
                                        class="text-danger">*</span></label>
                                <input type="time" class="form-control @error('start_time') is-invalid @enderror"
                                    id="start_time" name="start_time" value="{{ old('start_time') }}" required>
                                @error('start_time')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="end_time" class="form-label">End Time <span class="text-danger">*</span></label>
                                <input type="time" class="form-control @error('end_time') is-invalid @enderror"
                                    id="end_time" name="end_time" value="{{ old('end_time') }}" required>
                                @error('end_time')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="capacity" class="form-label">Capacity <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('capacity') is-invalid @enderror"
                                    id="capacity" name="capacity" value="{{ old('capacity') }}" min="1" required>
                                @error('capacity')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        @if ($isSport)
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="sport_level" class="form-label">Sport Level</label>
                                    <select class="form-select @error('sport_level') is-invalid @enderror"
                                        id="sport_level" name="sport_level">
                                        <option value="">Select Level (Optional)</option>
                                        @foreach ($levels as $level)
                                            <option value="{{ $level->name }}"
                                                {{ old('sport_level') == $level->name ? 'selected' : '' }}>
                                                {{ $level->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('sport_level')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="mb-3">
                        <label
                            class="form-label">{{ auth()->user()->school->institute_type === 'sport' ? 'Assign Coaches' : 'Assign Teachers' }}</label>
                        <div class="border rounded p-3 bg-light" style="max-height: 200px; overflow-y: auto;">
                            @foreach ($teachers as $teacher)
                                <div class="form-check mb-2">
                                    <input class="form-check-input @error('teacher_ids') is-invalid @enderror"
                                        type="checkbox" name="teacher_ids[]" value="{{ $teacher->id }}"
                                        id="teacher_{{ $teacher->id }}"
                                        {{ in_array($teacher->id, old('teacher_ids', [])) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="teacher_{{ $teacher->id }}">
                                        {{ $teacher->user->name }} <span
                                            class="text-muted small">({{ $teacher->employee_id }})</span>
                                    </label>
                                </div>
                            @endforeach
                        </div>
                        <small class="text-muted">Select one or more
                            {{ auth()->user()->school->institute_type === 'sport' ? 'coaches' : 'teachers' }} by checking
                            the boxes</small>
                        @error('teacher_ids')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label
                            class="form-label">{{ auth()->user()->school->institute_type === 'sport' ? 'Assign Athletes' : 'Assign Students' }}</label>
                        <div class="border rounded p-3 bg-light" style="max-height: 220px; overflow-y: auto;">
                            @forelse($students as $student)
                                <div class="form-check mb-2">
                                    <input class="form-check-input @error('student_ids') is-invalid @enderror"
                                        type="checkbox" name="student_ids[]" value="{{ $student->id }}"
                                        id="student_{{ $student->id }}"
                                        {{ in_array($student->id, old('student_ids', [])) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="student_{{ $student->id }}">
                                        {{ $student->user->name }}
                                        @if ($student->roll_number)
                                            <span class="text-muted small">({{ $student->roll_number }})</span>
                                        @endif
                                    </label>
                                </div>
                            @empty
                                <div class="text-muted small">No active
                                    {{ auth()->user()->school->institute_type === 'sport' ? 'athletes' : 'students' }}
                                    found.</div>
                            @endforelse
                        </div>
                        <small class="text-muted">Select one or more
                            {{ auth()->user()->school->institute_type === 'sport' ? 'athletes' : 'students' }} by checking
                            the boxes</small>
                        @error('student_ids')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle"></i>
                            {{ auth()->user()->school->institute_type === 'sport' ? 'Create Batch' : 'Create Batch' }}
                        </button>
                        <a href="{{ route('school.batches.index') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const courseSelect = document.getElementById('course_id');
            const subjectSelect = document.getElementById('subject_id');
            const classSelect = document.getElementById('class_id');

            const allSubjects = subjectSelect ? Array.from(subjectSelect.options) : [];
            const allClasses = classSelect ? Array.from(classSelect.options) : [];

            function filterClasses() {
                if (!classSelect || !courseSelect) return;
                const selectedCourseId = courseSelect.value;
                const currentClassId = classSelect.value;
                
                classSelect.innerHTML = '';
                allClasses.forEach(option => {
                    if (option.value === "" || option.getAttribute('data-course') === selectedCourseId) {
                        classSelect.appendChild(option.cloneNode(true));
                    }
                });
                classSelect.value = currentClassId;
                filterSubjects(); // Filter subjects whenever classes are filtered
            }

            function filterSubjects() {
                if (!subjectSelect) return;
                const currentSubjectId = subjectSelect.value;
                subjectSelect.innerHTML = '';

                if (@json($isSport)) {
                    // Sport mode: filter subjects by Course
                    const selectedCourseId = courseSelect.value;
                    allSubjects.forEach(option => {
                        if (option.value === "" || option.getAttribute('data-course') === selectedCourseId) {
                            subjectSelect.appendChild(option.cloneNode(true));
                        }
                    });
                } else {
                    // Academic mode: filter subjects by Class
                    const selectedClassId = classSelect.value;
                    allSubjects.forEach(option => {
                        if (option.value === "" || option.getAttribute('data-class') === selectedClassId) {
                            subjectSelect.appendChild(option.cloneNode(true));
                        }
                    });
                }
                subjectSelect.value = currentSubjectId;
            }

            if (courseSelect) {
                courseSelect.addEventListener('change', filterClasses);
            }
            if (classSelect) {
                classSelect.addEventListener('change', filterSubjects);
            }

            // Initial run
            if (courseSelect) filterClasses();
            else if (classSelect) filterSubjects();

            // Time validation logic
            const startTimeInput = document.getElementById('start_time');
            const endTimeInput = document.getElementById('end_time');

            function validateTimes() {
                const startTime = startTimeInput.value;
                const endTime = endTimeInput.value;

                // Clear previous validation states
                startTimeInput.classList.remove('is-invalid');
                endTimeInput.classList.remove('is-invalid');
                
                // Remove existing custom error messages
                const existingErrors = document.querySelectorAll('.time-error-msg');
                existingErrors.forEach(err => err.remove());

                if (startTime && endTime) {
                    if (endTime <= startTime) {
                        endTimeInput.classList.add('is-invalid');
                        const errorDiv = document.createElement('div');
                        errorDiv.className = 'invalid-feedback time-error-msg';
                        errorDiv.innerText = 'End time must be after start time.';
                        endTimeInput.parentNode.appendChild(errorDiv);
                        return false;
                    }
                }
                return true;
            }

            if (startTimeInput && endTimeInput) {
                startTimeInput.addEventListener('change', validateTimes);
                endTimeInput.addEventListener('change', validateTimes);
            }

            // Also check on form submit to prevent invalid submission
            const form = document.querySelector('form');
            if (form) {
                form.addEventListener('submit', function(e) {
                    if (!validateTimes()) {
                        e.preventDefault();
                        alert('Please fix the errors before submitting.');
                    }
                });
            }
        });
    </script>
@endsection
