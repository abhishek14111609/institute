@extends('layouts.app')

@section('title', $isSport ? 'Training Schedule' : 'Class Schedule')

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
        <div class="row g-4 mb-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm rounded-4 bg-white p-3">
                    <div class="card-body p-4 d-flex align-items-center justify-content-between flex-wrap">
                        <div class="d-flex align-items-center mb-3 mb-md-0">
                            <div class="bg-primary bg-opacity-10 p-4 rounded-4 me-4 d-flex align-items-center justify-content-center"
                                style="width: 80px; height: 80px;">
                                <i class="bi bi-clock-history text-primary display-6"></i>
                            </div>
                            <div>
                                <h3 class="fw-bold mb-1">{{ $isSport ? 'Weekly Training Overview' : 'Assigned Session Timetable' }}</h3>
                                <p class="text-muted mb-0">{{ $student->school->name }}</p>
                            </div>
                        </div>
                        <div class="d-flex gap-2">
                            <div class="bg-light p-3 rounded-4 px-4 border text-center">
                                <small class="tiny text-muted fw-bold d-block text-uppercase">Active {{ $label['batches'] }}</small>
                                <span class="fw-bold text-dark">{{ $displayBatches->count() }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                    <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                        <h5 class="fw-bold mb-0">Assigned Sessions</h5>
                        <p class="text-muted small mb-0">This timetable is based on the batches assigned by your school.</p>
                    </div>
                    <div class="card-body p-4">
                        @if($displayBatches->isNotEmpty())
                            <div class="table-responsive rounded-4 border overflow-hidden">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="bg-light">
                                        <tr>
                                            <th class="ps-4 small">BATCH</th>
                                            <th class="small">{{ $isSport ? 'LEVEL / GROUP' : 'CLASS' }}</th>
                                            <th class="small">TIME</th>
                                            <th class="small pe-4">INSTRUCTOR</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($displayBatches->sortBy('start_time') as $batch)
                                            <tr>
                                                <td class="ps-4">
                                                    <div class="fw-bold text-dark">{{ $batch->name }}</div>
                                                    <div class="tiny text-muted">{{ ucfirst($batch->class->type) }} session</div>
                                                </td>
                                                <td class="small text-muted">{{ $batch->class->name }}</td>
                                                <td class="small">
                                                    {{ $batch->start_time ? $batch->start_time->format('h:i A') : 'N/A' }}
                                                    @if($batch->end_time)
                                                        - {{ $batch->end_time->format('h:i A') }}
                                                    @endif
                                                </td>
                                                <td class="pe-4 small">
                                                    @if($batch->teachers->isNotEmpty())
                                                        {{ $batch->teachers->pluck('user.name')->implode(', ') }}
                                                    @else
                                                        <span class="text-muted">Not assigned</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="alert alert-warning border-0 rounded-4 mb-0">
                                No active timetable is available yet. Please contact the school admin.
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-header bg-white border-0 pt-4 px-4">
                        <h5 class="fw-bold mb-0">Schedule Notes</h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="d-flex align-items-start mb-3">
                            <i class="bi bi-info-circle-fill text-primary fs-4 me-3"></i>
                            <p class="small text-muted mb-0">Arrival time, room changes, and substitutes are managed by your school administration.</p>
                        </div>
                        <div class="d-flex align-items-start">
                            <i class="bi bi-bell-fill text-warning fs-4 me-3"></i>
                            <p class="small text-muted mb-0">If your assigned batch list looks incomplete, ask the admin to review your current enrollment.</p>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm rounded-4 bg-primary bg-opacity-10">
                    <div class="card-body p-4">
                        <h6 class="fw-bold mb-2">Need Help?</h6>
                        <p class="text-secondary small mb-0">Use the profile page to confirm your batches and contact details, then report any schedule issue to the office.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
