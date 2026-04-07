@extends('layouts.app')

@section('title', 'Add Course-wise Subjects')

@section('sidebar')
    @include('school.sidebar')
@endsection

@section('content')
    <div class="container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="mb-0">Add Course-wise Subjects</h3>
            <a href="{{ route('school.subject-templates.index') }}" class="btn btn-secondary">Back</a>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="card">
            <div class="card-body">
                <form action="{{ route('school.subject-templates.store') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label for="course_id" class="form-label">Course <span class="text-danger">*</span></label>
                        <select name="course_id" id="course_id" class="form-select @error('course_id') is-invalid @enderror"
                            required>
                            <option value="">Select Course</option>
                            @foreach ($courses as $course)
                                <option value="{{ $course->id }}" {{ old('course_id') == $course->id ? 'selected' : '' }}>
                                    {{ $course->name }} {{ $course->code ? '(' . $course->code . ')' : '' }}
                                </option>
                            @endforeach
                        </select>
                        @error('course_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <label class="form-label mb-0">Subjects <span class="text-danger">*</span></label>
                            <button type="button" class="btn btn-sm btn-outline-primary" id="add-subject-row">
                                <i class="bi bi-plus-lg"></i> Add More Subject
                            </button>
                        </div>

                        <div id="subject-rows" class="d-grid gap-2">
                            @php
                                $subjectRows = old('subject_names', ['']);
                                if (!is_array($subjectRows)) {
                                    $subjectRows = [$subjectRows];
                                }
                            @endphp

                            @foreach ($subjectRows as $index => $subjectRow)
                                <div class="input-group subject-row">
                                    <input type="text" name="subject_names[]"
                                        class="form-control @error('subject_names.' . $index) is-invalid @enderror"
                                        value="{{ $subjectRow }}" placeholder="Enter subject name"
                                        {{ $index === 0 ? 'required' : '' }}>
                                    <button type="button" class="btn btn-outline-danger remove-subject-row"
                                        {{ $index === 0 ? 'style=display:none' : '' }}>
                                        <i class="bi bi-dash-lg"></i>
                                    </button>
                                </div>
                                @error('subject_names.' . $index)
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            @endforeach
                        </div>

                        <small class="text-muted d-block mt-2">Use the plus button to add more subjects. Leave no blank
                            rows.</small>
                        @error('subject_names')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Default Description</label>
                        <textarea name="description" id="description" rows="3"
                            class="form-control @error('description') is-invalid @enderror"
                            placeholder="Optional description to apply to entered subjects">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-primary">Save Course-wise Subjects</button>
                </form>
            </div>
        </div>
    </div>

    <template id="subject-row-template">
        <div class="input-group subject-row">
            <input type="text" name="subject_names[]" class="form-control" placeholder="Enter subject name">
            <button type="button" class="btn btn-outline-danger remove-subject-row">
                <i class="bi bi-dash-lg"></i>
            </button>
        </div>
    </template>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const rowsContainer = document.getElementById('subject-rows');
            const addButton = document.getElementById('add-subject-row');
            const template = document.getElementById('subject-row-template');

            function updateRemoveButtons() {
                const rows = rowsContainer.querySelectorAll('.subject-row');
                rows.forEach((row, index) => {
                    const removeButton = row.querySelector('.remove-subject-row');
                    if (removeButton) {
                        removeButton.style.display = index === 0 ? 'none' : '';
                    }
                });
            }

            addButton.addEventListener('click', function() {
                const clone = template.content.cloneNode(true);
                rowsContainer.appendChild(clone);
                updateRemoveButtons();
            });

            rowsContainer.addEventListener('click', function(event) {
                const removeButton = event.target.closest('.remove-subject-row');
                if (!removeButton) {
                    return;
                }

                const row = removeButton.closest('.subject-row');
                if (row) {
                    row.remove();
                    if (!rowsContainer.querySelector('.subject-row')) {
                        rowsContainer.insertAdjacentHTML('beforeend', template.innerHTML);
                    }
                    updateRemoveButtons();
                }
            });

            updateRemoveButtons();
        });
    </script>
@endsection
