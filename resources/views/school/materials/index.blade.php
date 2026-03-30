@extends('layouts.app')

@section('title', $label['materials'] . ' Hub')

@section('sidebar')
    @include('school.sidebar')
@endsection

@section('content')
    <div class="container-fluid text-dark">
        <div class="row g-4 m-0">
            <!-- Sidebar / Quick Folders -->
            <div class="col-xl-3">
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-body p-4">
                        <h6 class="fw-bold mb-4 opacity-75">QUICK FOLDERS</h6>
                        <div class="list-group list-group-flush gap-2">
                            <a href="{{ route('school.materials.index') }}"
                                class="list-group-item list-group-item-action border-0 rounded-3 d-flex align-items-center {{ !request('batch_id') ? 'bg-primary bg-opacity-10 text-primary fw-bold' : '' }}">
                                <i class="bi bi-collection-fill me-3"></i> All {{ $label['materials'] }}
                            </a>
                            @foreach($batches as $batch)
                                <a href="{{ route('school.materials.index', ['batch_id' => $batch->id]) }}"
                                    class="list-group-item list-group-item-action border-0 rounded-3 d-flex align-items-center {{ request('batch_id') == $batch->id ? 'bg-primary bg-opacity-10 text-primary fw-bold' : '' }}">
                                    <i class="bi bi-folder-fill me-3 text-warning"></i> {{ $batch->name }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow rounded-4 bg-primary text-white overflow-hidden">
                    <div class="card-body p-4 position-relative z-1">
                        <i class="bi bi-cloud-upload display-6 opacity-25 float-end"></i>
                        <h5 class="fw-bold mb-3 font-outfit">Upload {{ $label['materials'] }}</h5>
                        <p class="small opacity-75 mb-4">Add resources, notes, or digital inventory items for students.
                        </p>
                        <button class="btn btn-white text-primary w-100 fw-bold rounded-pill shadow-sm" data-bs-toggle="modal"
                            data-bs-target="#uploadModal">
                            <i class="bi bi-plus-lg me-1"></i> New Upload
                        </button>
                    </div>
                    <div class="position-absolute end-0 bottom-0 w-50 h-50 bg-white opacity-10"
                        style="clip-path: circle(100% at 100% 100%);"></div>
                </div>
            </div>

            <!-- Main File Browser -->
            <div class="col-xl-9">
                <div class="card border-0 shadow-sm rounded-4 min-vh-100">
                    <div class="card-header bg-white border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                        <h5 class="fw-bold mb-0 font-outfit">Documents & Digital Stock</h5>
                        <div class="d-flex gap-2">
                            <form action="{{ route('school.materials.index') }}" method="GET" class="position-relative">
                                @if(request('batch_id')) <input type="hidden" name="batch_id"
                                value="{{ request('batch_id') }}"> @endif
                                <input type="text" name="search"
                                    class="form-control form-control-sm ps-5 bg-light border-0 rounded-pill"
                                    placeholder="Search files..." value="{{ request('search') }}">
                                <i
                                    class="bi bi-search position-absolute top-50 start-0 translate-middle-y ms-3 opacity-50"></i>
                            </form>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <div class="table-responsive">
                            <table class="table align-middle">
                                <thead>
                                    <tr class="text-muted small">
                                        <th class="border-0 ps-0">FILE NAME</th>
                                        <th class="border-0">UPLOADED BY</th>
                                        <th class="border-0">ASSIGNED</th>
                                        <th class="border-0">SIZE</th>
                                        <th class="border-0">DATE</th>
                                        <th class="border-0 text-end">ACTIONS</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($materials as $material)
                                        <tr>
                                            <td class="ps-0 border-light py-3">
                                                <div class="d-flex align-items-center">
                                                    @php
                                                        $iconClass = 'bi-file-earmark-text';
                                                        $bgClass = 'bg-secondary';
                                                        if (in_array($material->file_type, ['jpg', 'png', 'jpeg'])) {
                                                            $iconClass = 'bi-file-earmark-image';
                                                            $bgClass = 'bg-success';
                                                        } elseif ($material->file_type == 'pdf') {
                                                            $iconClass = 'bi-file-earmark-pdf';
                                                            $bgClass = 'bg-danger';
                                                        } elseif ($material->file_type == 'zip') {
                                                            $iconClass = 'bi-file-earmark-zip';
                                                            $bgClass = 'bg-warning';
                                                        } elseif ($material->file_type == 'mp4') {
                                                            $iconClass = 'bi-file-earmark-play';
                                                            $bgClass = 'bg-primary';
                                                        }
                                                    @endphp
                                                    <div class="p-2 {{ $bgClass }} bg-opacity-10 text-{{ str_replace('bg-', '', $bgClass) }} rounded-3 me-3 fs-4 d-flex align-items-center justify-content-center"
                                                        style="width: 45px; height: 45px;">
                                                        <i class="bi {{ $iconClass }}"></i>
                                                    </div>
                                                    <div>
                                                        <h6 class="fw-bold mb-0 text-dark">{{ $material->title }}</h6>
                                                        <small
                                                            class="text-muted opacity-75">.{{ strtoupper($material->file_type) }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="border-light py-3">
                                                <div class="d-flex align-items-center">
                                                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 24px; height: 24px; font-size: 0.7rem;">
                                                        {{ substr($material->teacher->name ?? 'A', 0, 1) }}
                                                    </div>
                                                    <span class="small">{{ $material->teacher->name ?? 'Admin' }}</span>
                                                </div>
                                            </td>
                                            <td class="border-light py-3">
                                                <span
                                                    class="badge bg-light text-dark fw-bold border">{{ $material->batch->name ?? 'Global' }}</span>
                                            </td>
                                            <td class="border-light py-3 small">{{ $material->readable_size }}</td>
                                            <td class="border-light py-3 small text-muted">
                                                {{ $material->created_at->format('M d, Y') }}</td>
                                            <td class="border-light py-3 text-end pe-0">
                                                <div class="dropdown">
                                                    <button class="btn btn-icon btn-light rounded-circle shadow-none"
                                                        data-bs-toggle="dropdown">
                                                        <i class="bi bi-three-dots-vertical"></i>
                                                    </button>
                                                    <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg">
                                                        <li><a class="dropdown-item py-2"
                                                                href="{{ route('school.materials.download', $material) }}"><i
                                                                    class="bi bi-download me-2 text-primary"></i> Download</a></li>
                                                        <li>
                                                            <hr class="dropdown-divider">
                                                        </li>
                                                        <li>
                                                            <form action="{{ route('school.materials.destroy', $material) }}"
                                                                method="POST"
                                                                onsubmit="return confirm('Permanently remove this resource?')">
                                                                @csrf @method('DELETE')
                                                                <button type="submit" class="dropdown-item py-2 text-danger"><i
                                                                        class="bi bi-trash3 me-2"></i> Delete</button>
                                                            </form>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center py-5">
                                                <div class="py-5 opacity-25">
                                                    <i class="bi bi-database-fill-exclamation display-1 d-block mb-3"></i>
                                                    <h6 class="fw-bold">No {{ $label['materials'] }} found.</h6>
                                                    <p class="small">Upload shared documents or view teacher resources here.</p>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                            <div class="mt-4">
                                {{ $materials->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Upload Modal -->
    <div class="modal fade" id="uploadModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-header border-0 pt-4 px-4">
                    <h5 class="fw-bold mb-0">New {{ $label['materials'] }} Entry</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('school.materials.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted">TITLE / NAME</label>
                            <input type="text" name="title" class="form-control rounded-3" placeholder="e.g. Annual Syllabus 2024"
                                required>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label small fw-bold text-muted">BATCH (OPTIONAL)</label>
                                <select name="batch_id" class="form-select rounded-3">
                                    <option value="">— Global Resource —</option>
                                    @foreach($batches as $batch)
                                        <option value="{{ $batch->id }}">{{ $batch->name }} ({{ $batch->class->name }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label small fw-bold text-muted">ATTRIB. TEACHER</label>
                                <select name="teacher_id" class="form-select rounded-3">
                                    <option value="{{ auth()->id() }}">Admin (Self)</option>
                                    @foreach($teachers as $teacher)
                                        <option value="{{ $teacher->id }}">{{ $teacher->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="mb-0">
                            <label class="form-label small fw-bold text-muted">UPLOAD FILE</label>
                            <input type="file" name="file" class="form-control rounded-3" required>
                            <small class="text-muted mt-1 d-block">Max: 50MB. All formats supported.</small>
                        </div>
                    </div>
                    <div class="modal-footer border-0 pb-4 px-4 pt-0">
                        <button type="button" class="btn btn-light rounded-pill px-4"
                            data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary rounded-pill px-4 shadow">Upload to Stock</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <style>
        .font-outfit { font-family: 'Outfit', sans-serif; }
        .btn-white { background: white; color: var(--bs-primary); border: none; }
        .btn-white:hover { background: #f8f9fa; }
        .list-group-item-action:hover { background-color: rgba(var(--bs-primary-rgb), 0.05); }
    </style>
@endsection
