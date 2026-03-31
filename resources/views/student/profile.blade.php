@extends('layouts.app')

@section('title', 'My Profile')

@section('sidebar')
    @include('student.sidebar')
@endsection

@section('content')
    @php
        $displayBatches = $student->batches->isNotEmpty()
            ? $student->batches
            : collect([$student->batch])->filter();
    @endphp

    <div class="container-fluid">
        <div class="row g-4">
            <div class="col-xl-4">
                <div class="card border-0 shadow-lg overflow-hidden position-relative mb-4" style="border-radius: 2rem;">
                    <div class="bg-primary position-absolute w-100 h-25 top-0 start-0 z-0" style="background: var(--primary-gradient) !important;"></div>
                    <div class="card-body p-5 position-relative z-1 text-center mt-4">
                        <div class="position-relative d-inline-block mb-4">
                            @if($student->user->avatar)
                                <img src="{{ asset('storage/' . $student->user->avatar) }}" alt="Avatar"
                                    class="rounded-circle shadow-lg border-4 border-white" width="140" height="140"
                                    style="object-fit: cover;">
                            @else
                                <div class="rounded-circle bg-white text-primary d-flex align-items-center justify-content-center border-4 border-white mx-auto shadow-lg"
                                    style="width: 140px; height: 140px; font-size: 4rem; font-weight: 800;">
                                    {{ substr($student->user->name, 0, 1) }}
                                </div>
                            @endif
                            <span class="position-absolute bottom-0 end-0 bg-success border-2 border-white rounded-circle"
                                style="width: 25px; height: 25px;"></span>
                        </div>

                        <h3 class="fw-bold mb-1 text-gradient">{{ $student->user->name }}</h3>
                        <p class="text-muted small mb-3">Roll: {{ $student->roll_number ?? 'Pending' }}</p>

                        <div class="d-flex justify-content-center gap-2 mb-4">
                            <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-2">Active {{ $isSport ? 'Athlete' : 'Student' }}</span>
                            <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-2">{{ $student->school->name }}</span>
                        </div>

                        <div class="row g-2 border-top border-light pt-4 mt-2">
                            <div class="col-6 border-end">
                                <h6 class="fw-bold mb-0">ID NO.</h6>
                                <small class="text-muted">{{ 'STD-' . str_pad($student->id, 4, '0', STR_PAD_LEFT) }}</small>
                            </div>
                            <div class="col-6">
                                <h6 class="fw-bold mb-0">DOB</h6>
                                <small class="text-muted">{{ $student->birth_date ? $student->birth_date->format('d M, Y') : 'N/A' }}</small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-0 pt-4">
                        <h5 class="fw-bold mb-0">Contact Details</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="p-2 bg-light rounded-3 me-3 text-primary"><i class="bi bi-envelope"></i></div>
                            <div>
                                <small class="text-muted d-block">PRIMARY EMAIL</small>
                                <span class="fw-semibold">{{ $student->user->email }}</span>
                            </div>
                        </div>
                        <div class="d-flex align-items-center mb-3">
                            <div class="p-2 bg-light rounded-3 me-3 text-success"><i class="bi bi-telephone"></i></div>
                            <div>
                                <small class="text-muted d-block">{{ $isSport ? 'ATHLETE CONTACT' : 'STUDENT CONTACT' }}</small>
                                <span class="fw-semibold">{{ $student->user->phone ?? 'Not provided' }}</span>
                            </div>
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="p-2 bg-light rounded-3 me-3 text-info"><i class="bi bi-shield-check"></i></div>
                            <div>
                                <small class="text-muted d-block">PARENT/GUARDIAN CONTACT</small>
                                <span class="fw-semibold">{{ $student->parent_phone ?? 'N/A' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-8">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-0 pt-4 d-flex justify-content-between align-items-center">
                        <h5 class="fw-bold mb-0">{{ $isSport ? 'Assigned Training Groups' : 'Academic Assignment' }}</h5>
                        <i class="bi bi-mortarboard fs-4 text-primary opacity-50"></i>
                    </div>
                    <div class="card-body p-4">
                        @if($displayBatches->isNotEmpty())
                            <div class="row g-4">
                                @foreach($displayBatches as $batch)
                                    <div class="col-lg-6">
                                        <div class="bg-primary bg-opacity-10 rounded-4 p-4 border border-primary border-opacity-10 h-100">
                                            <h5 class="fw-bold text-primary mb-1">{{ $batch->name }}</h5>
                                            <p class="text-muted mb-2">{{ $batch->class->name }}</p>
                                            <div class="small text-muted mb-3">
                                                <i class="bi bi-clock me-1"></i>
                                                {{ $batch->start_time ? $batch->start_time->format('h:i A') : 'N/A' }}
                                                @if($batch->end_time)
                                                    - {{ $batch->end_time->format('h:i A') }}
                                                @endif
                                            </div>
                                            <div class="d-flex flex-wrap gap-2">
                                                @forelse($batch->teachers as $teacher)
                                                    <span class="badge bg-white text-primary border rounded-pill px-3 py-2">
                                                        {{ $teacher->user->name }}
                                                    </span>
                                                @empty
                                                    <span class="badge bg-white text-muted border rounded-pill px-3 py-2">
                                                        Instructor not assigned
                                                    </span>
                                                @endforelse
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="alert alert-warning border-0 rounded-4 p-4 d-flex align-items-center">
                                <i class="bi bi-exclamation-triangle-fill fs-3 me-3"></i>
                                <div>
                                    <h6 class="fw-bold mb-1">No Active Batch</h6>
                                    <p class="mb-0 small">Your school has not assigned an active batch yet. Please contact the admin office.</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-header bg-white border-0 pt-4">
                                <h5 class="fw-bold mb-0">Admission Summary</h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-4">
                                    <label class="text-muted tiny fw-bold text-uppercase" style="font-size: 0.7rem; letter-spacing: 1px;">ENROLLMENT DATE</label>
                                    <p class="fw-bold text-dark mb-0">{{ $student->admission_date ? $student->admission_date->format('M d, Y') : 'N/A' }}</p>
                                </div>
                                <div>
                                    <label class="text-muted tiny fw-bold text-uppercase" style="font-size: 0.7rem; letter-spacing: 1px;">{{ $student->school->institute_type === 'sport' ? 'CURRENT SCHOOL / INSTITUTE' : 'PREVIOUS ACADEMIC RECORD' }}</label>
                                    <p class="fw-bold text-dark mb-0 text-truncate">{{ $student->previous_school ?? 'Fresh Enrollment' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-header bg-white border-0 pt-4">
                                <h5 class="fw-bold mb-0">Residential Address</h5>
                            </div>
                            <div class="card-body">
                                <div class="d-flex">
                                    <i class="bi bi-geo-alt text-danger fs-4 me-3 mt-1"></i>
                                    <p class="mb-0 text-muted opacity-85" style="line-height: 1.6;">
                                        {{ $student->address ?? 'Address not updated in records. Please update via settings or contact admin.' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
