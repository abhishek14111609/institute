@extends('layouts.app')

@section('title', 'Course-wise Subjects')

@section('sidebar')
    @include('school.sidebar')
@endsection

@section('content')
    <div class="container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="mb-1">Course-wise Subjects</h3>
                <p class="text-muted mb-0">Add subjects once per course, then select them while creating classes.</p>
            </div>
            <a href="{{ route('school.subject-templates.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Add Course Subjects
            </a>
        </div>

        <div class="card">
            <div class="table-responsive">
                <table class="table table-striped mb-0">
                    <thead>
                        <tr>
                            <th>Course</th>
                            <th>Subject</th>
                            <th>Description</th>
                            <th>Status</th>
                            <th>Used In Classes</th>
                            <th class="text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($templates as $template)
                            <tr>
                                <td>{{ $template->course?->name ?? 'N/A' }}</td>
                                <td>{{ $template->name }}</td>
                                <td>{{ $template->description ?: '-' }}</td>
                                <td>
                                    <span class="badge bg-{{ $template->is_active ? 'success' : 'secondary' }}">
                                        {{ $template->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td>{{ $template->subjects_count }}</td>
                                <td class="text-end">
                                    <a href="{{ route('school.subject-templates.edit', $template) }}"
                                        class="btn btn-sm btn-outline-warning">Edit</a>
                                    <form action="{{ route('school.subject-templates.destroy', $template) }}" method="POST"
                                        class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger"
                                            onclick="return confirm('Delete this course-wise subject?')">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4">
                                    No course-wise subjects found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-3">
            {{ $templates->links() }}
        </div>
    </div>
@endsection
