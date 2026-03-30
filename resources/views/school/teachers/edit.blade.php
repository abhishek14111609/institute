@extends('layouts.app')

@section('title', auth()->user()->school->institute_type === 'sport' ? 'Edit Coach' : 'Edit Teacher')

@section('sidebar')
    @include('school.sidebar')
@endsection

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>{{ auth()->user()->school->institute_type === 'sport' ? 'Edit Coach/Staff:' : 'Edit Teacher:' }} {{ $teacher->user->name }}</h2>
        <a href="{{ route('school.teachers.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Back
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('school.teachers.update', $teacher) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PATCH')

                <h5 class="mb-3">Personal Information</h5>
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                   id="name" name="name" value="{{ old('name', $teacher->user->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror"
                                   id="email" name="email" value="{{ old('email', $teacher->user->email) }}" required
                                   data-ajax-validate="true" data-table="users" data-rules="required|email"
                                   data-user-id="{{ $teacher->user_id }}">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="username" class="form-label">Username <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('username') is-invalid @enderror"
                                   id="username" name="username" value="{{ old('username', $teacher->user->username) }}" required
                                   data-ajax-validate="true" data-table="users" data-rules="required|alpha_dash|min:3"
                                   data-user-id="{{ $teacher->user_id }}">
                            @error('username')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="phone" class="form-label">Phone</label>
                            <input type="tel" class="form-control @error('phone') is-invalid @enderror"
                                   id="phone" name="phone" value="{{ old('phone', $teacher->user->phone) }}"
                                   data-ajax-validate="true" data-table="users" data-rules="nullable|numeric|max:15"
                                   data-user-id="{{ $teacher->user_id }}"
                                   placeholder="e.g. 9876543210">
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror"
                                   id="password" name="password">
                            <small class="text-muted">Leave blank to keep current password</small>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">Confirm Password</label>
                            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation">
                        </div>
                    </div>
                </div>

                <hr class="my-4">

                <h5 class="mb-3">{{ auth()->user()->school->institute_type === 'sport' ? 'Coaching/Staff Information' : 'Professional Information' }}</h5>
                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="employee_id" class="form-label">{{ auth()->user()->school->institute_type === 'sport' ? 'Staff/Coach ID' : 'Employee ID' }}</label>
                            <input type="text" class="form-control bg-light"
                                   id="employee_id" name="employee_id" value="{{ old('employee_id', $teacher->employee_id) }}" readonly>
                            @error('employee_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="joining_date" class="form-label">Joining Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('joining_date') is-invalid @enderror"
                                   id="joining_date" name="joining_date" value="{{ old('joining_date', $teacher->joining_date->format('Y-m-d')) }}" required>
                            @error('joining_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="salary" class="form-label">Salary</label>
                            <input type="number" class="form-control @error('salary') is-invalid @enderror"
                                   id="salary" name="salary" value="{{ old('salary', $teacher->salary) }}" step="0.01">
                            @error('salary')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="qualification" class="form-label">Qualification</label>
                            <input type="text" class="form-control @error('qualification') is-invalid @enderror"
                                   id="qualification" name="qualification" value="{{ old('qualification', $teacher->qualification) }}">
                            @error('qualification')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="specialization" class="form-label">Specialization</label>
                            <input type="text" class="form-control @error('specialization') is-invalid @enderror"
                                   id="specialization" name="specialization" value="{{ old('specialization', $teacher->specialization) }}">
                            @error('specialization')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="avatar" class="form-label">Profile Photo</label>
                    <div class="mb-2">
                        @if($teacher->user->avatar)
                            <img src="{{ asset('storage/' . $teacher->user->avatar) }}" alt="Avatar" class="img-thumbnail" width="100">
                        @endif
                    </div>
                    <input type="file" class="form-control @error('avatar') is-invalid @enderror"
                           id="avatar" name="avatar" accept="image/*">
                    @error('avatar')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">{{ auth()->user()->school->institute_type === 'sport' ? 'Assign Sessions' : 'Assign Batches' }}</label>
                    <div class="border rounded p-3 bg-light" style="max-height: 200px; overflow-y: auto;">
                        @foreach($batches as $batch)
                            <div class="form-check mb-2">
                                <input class="form-check-input @error('batches') is-invalid @enderror" type="checkbox" name="batches[]" value="{{ $batch->id }}" id="batch_{{ $batch->id }}" {{ in_array($batch->id, old('batches', $teacher->batches->pluck('id')->toArray())) ? 'checked' : '' }}>
                                <label class="form-check-label" for="batch_{{ $batch->id }}">
                                    {{ $batch->name }} <span class="text-muted small">({{ $batch->class->name }})</span>
                                </label>
                            </div>
                        @endforeach
                    </div>
                    <small class="text-muted">Select one or more {{ auth()->user()->school->institute_type === 'sport' ? 'sessions' : 'batches' }} by checking the boxes</small>
                    @error('batches')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1"
                               {{ old('is_active', $teacher->is_active) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_active">
                            Active
                        </label>
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle"></i> {{ auth()->user()->school->institute_type === 'sport' ? 'Update Coach' : 'Update Teacher' }}
                    </button>
                    <a href="{{ route('school.teachers.index') }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Phone number numeric validation
        const phoneInput = document.querySelector('input[name="phone"]');
        if (phoneInput) {
            phoneInput.addEventListener('input', function(e) {
                this.value = this.value.replace(/[^0-9]/g, '');
                if (this.value.length > 15) this.value = this.value.slice(0, 15);
            });
        }

        // Password matching validation
        const passwordInput = document.getElementById('password');
        const confirmInput = document.getElementById('password_confirmation');
        
        function validatePasswords() {
            if (confirmInput.value && passwordInput.value !== confirmInput.value) {
                confirmInput.classList.add('is-invalid');
                if (!confirmInput.nextElementSibling || !confirmInput.nextElementSibling.classList.contains('password-error')) {
                    const errorDiv = document.createElement('div');
                    errorDiv.className = 'invalid-feedback password-error';
                    errorDiv.innerText = 'Passwords do not match.';
                    confirmInput.parentNode.appendChild(errorDiv);
                }
                return false;
            } else {
                confirmInput.classList.remove('is-invalid');
                const errorMsg = confirmInput.parentNode.querySelector('.password-error');
                if (errorMsg) errorMsg.remove();
                return true;
            }
        }

        if (passwordInput && confirmInput) {
            passwordInput.addEventListener('input', validatePasswords);
            confirmInput.addEventListener('input', validatePasswords);
        }

        const form = document.querySelector('form');
        if (form) {
            form.addEventListener('submit', function(e) {
                if (!validatePasswords()) {
                    e.preventDefault();
                    alert('Passwords do not match. Please correct them.');
                }
            });
        }
    });
</script>
@endsection
