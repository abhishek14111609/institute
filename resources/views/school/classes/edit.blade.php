@extends('layouts.app')

@section('title', auth()->user()->school->institute_type === 'sport' ? 'Edit Team' : 'Edit Class')

@section('sidebar')
    @include('school.sidebar')
@endsection

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>{{ auth()->user()->school->institute_type === 'sport' ? 'Edit Team: ' : 'Edit Class: ' }}
                {{ $class->name }}</h2>
            <a href="{{ route('school.classes.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Back
            </a>
        </div>

        <div class="card">
            <div class="card-body">
                <form action="{{ route('school.classes.update', $class) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="course_id"
                                class="form-label">{{ auth()->user()->school->institute_type === 'sport' ? 'Program/Discipline' : 'Course' }}
                                <span class="text-muted">(Optional)</span></label>
                            <select class="form-select @error('course_id') is-invalid @enderror" id="course_id"
                                name="course_id">
                                <option value="">
                                    {{ auth()->user()->school->institute_type === 'sport' ? 'Select Discipline' : 'Select Course' }}
                                </option>
                                @foreach ($courses as $course)
                                    <option value="{{ $course->id }}"
                                        {{ old('course_id', $class->course_id) == $course->id ? 'selected' : '' }}>
                                        {{ $course->name }} {{ $course->code ? "($course->code)" : '' }}
                                    </option>
                                @endforeach
                            </select>
                            @error('course_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    @if (auth()->user()->school->institute_type !== 'sport')
                        <div class="mb-3" id="subject-template-wrapper" style="display:none;">
                            <label class="form-label">Course-wise Subjects</label>
                            <div class="border rounded p-3" style="max-height: 280px; overflow-y: auto;">
                                @php
                                    $selectedTemplateIds = old('subject_template_ids', $assignedTemplateIds ?? []);
                                @endphp
                                @forelse($subjectTemplates as $template)
                                    <div class="form-check subject-template-option"
                                        data-course="{{ $template->course_id }}" style="display:none;">
                                        <input class="form-check-input" type="checkbox" name="subject_template_ids[]"
                                            value="{{ $template->id }}" id="subject_template_{{ $template->id }}"
                                            {{ in_array($template->id, $selectedTemplateIds) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="subject_template_{{ $template->id }}">
                                            {{ $template->name }}
                                        </label>
                                    </div>
                                @empty
                                    <p class="text-muted mb-0">No course-wise subjects found. Add them from Course-wise
                                        Subjects first.</p>
                                @endforelse
                                <p class="text-muted mb-0 template-empty-note" style="display:none;">No course-wise subjects
                                    available for selected course.</p>
                            </div>
                            <small class="text-muted d-block mt-2">
                                Manage these from <a href="{{ route('school.subject-templates.index') }}">Course-wise
                                    Subjects</a>.
                            </small>
                        </div>
                    @endif

                    <div class="col-md-12">
                        <div class="mb-3">
                            <label for="name"
                                class="form-label">{{ auth()->user()->school->institute_type === 'sport' ? 'Team Name' : 'Class Name' }}
                                <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                                name="name" value="{{ old('name', $class->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <input type="hidden" name="type"
                        value="{{ auth()->user()->school->institute_type === 'sport' ? 'sports' : 'academic' }}">

                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1"
                                {{ old('is_active', $class->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">
                                Active
                            </label>
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle"></i>
                            {{ auth()->user()->school->institute_type === 'sport' ? 'Update Team' : 'Update Class' }}
                        </button>
                        <a href="{{ route('school.classes.index') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @if (auth()->user()->school->institute_type !== 'sport')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const courseSelect = document.getElementById('course_id');
                const wrapper = document.getElementById('subject-template-wrapper');
                const options = document.querySelectorAll('.subject-template-option');

                function filterTemplatesByCourse() {
                    const courseId = courseSelect ? courseSelect.value : '';
                    let visibleCount = 0;

                    options.forEach((item) => {
                        const isVisible = courseId && item.dataset.course === courseId;
                        item.style.display = isVisible ? '' : 'none';

                        if (isVisible) {
                            visibleCount++;
                        }
                    });

                    if (wrapper) {
                        wrapper.style.display = courseId ? '' : 'none';
                    }

                    const emptyNote = wrapper ? wrapper.querySelector('.template-empty-note') : null;
                    if (emptyNote) {
                        emptyNote.style.display = visibleCount === 0 ? '' : 'none';
                    }
                }

                if (courseSelect) {
                    courseSelect.addEventListener('change', filterTemplatesByCourse);
                    filterTemplatesByCourse();
                }
            });
        </script>
    @endif
@endsection
