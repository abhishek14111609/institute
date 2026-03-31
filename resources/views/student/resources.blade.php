@extends('layouts.app')

@section('title', 'Knowledge Hub')

@section('sidebar')
    @include('student.sidebar')
@endsection

@section('content')
    <div class="container-fluid">
        <!-- Premium Library Header -->
        <div class="row g-4 mb-5">
            <div class="col-12">
                <div class="card border-0 shadow-sm rounded-4 bg-dark text-white overflow-hidden p-2 position-relative">
                    <div class="card-body p-5 d-flex align-items-center justify-content-between position-relative z-1">
                        <div class="col-lg-7">
                            <h6 class="text-primary fw-bold tiny mb-2" style="letter-spacing: 2px;">Resource
                                Center</h6>
                            <h2 class="fw-bold mb-3 display-6">Knowledge Hub & Library</h2>
                            <p class="text-white-50 lead mb-0">Download strategic playbooks, research papers, and technical
                                drills curated specifically for your batch level.</p>
                        </div>
                        <div class="col-lg-4 d-none d-lg-block text-end">
                            <i class="bi bi-journal-bookmark-fill text-primary opacity-25" style="font-size: 150px;"></i>
                        </div>
                    </div>
                    <!-- Decorative background -->
                    <div class="position-absolute end-0 top-0 h-100 w-50 bg-primary opacity-10"
                        style="clip-path: polygon(100% 0, 0% 100%, 100% 100%);"></div>
                </div>
            </div>
        </div>

        <!-- Filter & Search Section -->
        <div class="row g-4 mb-4">
            <div class="col-12">
                <form action="{{ route('student.resources') }}" method="GET"
                    class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <div class="d-flex gap-2 pb-2 flex-wrap">
                        <a href="{{ route('student.resources') }}"
                            class="btn {{ !request('batch_id') ? 'btn-primary' : 'btn-white bg-white border text-muted' }} rounded-pill px-4 shadow-sm fw-bold">
                            All Materials
                        </a>
                        @foreach($batches as $batch)
                            <a href="{{ route('student.resources', ['batch_id' => $batch->id, 'search' => request('search')]) }}"
                                class="btn {{ (int) request('batch_id') === $batch->id ? 'btn-primary' : 'btn-white bg-white border text-muted' }} rounded-pill px-4 shadow-sm fw-bold">
                                {{ $batch->name }}
                            </a>
                        @endforeach
                    </div>
                    <div class="search-box position-relative d-flex gap-2" style="min-width: 300px;">
                        @if(request('batch_id'))
                            <input type="hidden" name="batch_id" value="{{ request('batch_id') }}">
                        @endif
                        <i class="bi bi-search position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
                        <input type="text" name="search" value="{{ request('search') }}"
                            class="form-control rounded-pill border-0 shadow-sm ps-5 py-2"
                            placeholder="Search by title...">
                        <button type="submit" class="btn btn-dark rounded-pill px-4 shadow-sm">Search</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Resources Grid -->
        <div class="row g-4 mb-5">
            @forelse($materials as $material)
                <div class="col-xl-4 col-md-6">
                    <div class="card border-0 shadow-sm rounded-4 h-100 transition-all hover-lift overflow-hidden bg-white">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-start mb-4">
                                @php
                                    $theme = [
                                        'pdf' => ['icon' => 'bi-file-earmark-pdf-fill', 'color' => 'danger'],
                                        'zip' => ['icon' => 'bi-file-earmark-zip-fill', 'color' => 'warning'],
                                        'mp4' => ['icon' => 'bi-play-circle-fill', 'color' => 'primary'],
                                        'jpg' => ['icon' => 'bi-file-image-fill', 'color' => 'success'],
                                        'jpeg' => ['icon' => 'bi-file-image-fill', 'color' => 'success'],
                                        'png' => ['icon' => 'bi-file-image-fill', 'color' => 'success'],
                                    ][$material->file_type] ?? ['icon' => 'bi-file-earmark-text-fill', 'color' => 'secondary'];
                                @endphp
                                <div class="bg-{{ $theme['color'] }} bg-opacity-10 p-3 rounded-4 shadow-sm">
                                    <i class="bi {{ $theme['icon'] }} text-{{ $theme['color'] }} fs-2"></i>
                                </div>
                                <div class="text-end">
                                    <span
                                        class="badge bg-light text-dark border rounded-pill px-2 py-1 tiny fw-bold">{{ strtoupper($material->file_type) }}</span>
                                    <div class="tiny text-muted mt-1 fw-bold">{{ $material->readable_size }}</div>
                                </div>
                            </div>

                            <h5 class="fw-bold text-dark mb-1">{{ $material->title }}</h5>
                            <p class="text-muted small mb-0">Instructor: <span
                                    class="fw-bold text-primary">{{ optional($material->teacher)->name ?? 'Department Office' }}</span>
                            </p>

                            <div class="mt-4 pt-4 border-top d-flex justify-content-between align-items-center">
                                <div class="small fw-bold text-muted">
                                    <i class="bi bi-calendar3 me-1"></i> {{ $material->created_at->format('M d, Y') }}
                                </div>
                                <a href="{{ route('student.materials.download', $material) }}"
                                    class="btn btn-dark rounded-pill px-4 btn-sm fw-bold shadow-sm">
                                    <i class="bi bi-cloud-arrow-down-fill me-1"></i> Retrieve
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 py-5">
                    <div class="text-center py-5 bg-white rounded-4 shadow-sm border border-dashed">
                        <div class="py-5 opacity-25">
                            <i class="bi bi-journal-x display-1 d-block mb-3"></i>
                            <h4 class="fw-bold text-dark">Library is Currently Empty</h4>
                            <p class="text-muted small mb-0">Materials assigned to your level will appear here automatically.
                            </p>
                        </div>
                    </div>
                </div>
            @endforelse
        </div>
    </div>
@endsection
