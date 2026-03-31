@extends('layouts.app')

@section('title', 'Portal Settings')

@section('sidebar')
    @include('teacher.sidebar')
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row g-4 mb-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm rounded-4 bg-dark text-white overflow-hidden p-2">
                    <div class="card-body p-4 d-flex align-items-center justify-content-between flex-wrap">
                        <div class="d-flex align-items-center">
                            <div class="bg-primary bg-opacity-10 p-3 rounded-circle me-3 d-flex align-items-center justify-content-center"
                                style="width: 60px; height: 60px;">
                                <i class="bi bi-gear-wide-connected text-white fs-3"></i>
                            </div>
                            <div>
                                <h4 class="fw-bold mb-0">Account Settings</h4>
                                <p class="text-white-50 mb-0 small">Manage your personal details and account security.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
                    <div class="card-header bg-white border-0 pt-4 px-4">
                        <h5 class="fw-bold mb-0">Profile Details</h5>
                    </div>
                    <div class="card-body p-4">
                        <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('patch')

                            <div class="d-flex align-items-center mb-4 p-4 rounded-4 bg-light border border-dashed">
                                <div class="position-relative me-4">
                                    @if(auth()->user()->avatar)
                                        <img src="{{ asset('storage/' . auth()->user()->avatar) }}"
                                            class="rounded-circle border-4 border-white shadow-lg"
                                            style="width: 100px; height: 100px; object-fit: cover;">
                                    @else
                                        <div class="rounded-circle bg-primary bg-opacity-10 d-flex align-items-center justify-content-center text-primary fw-bold shadow-sm border-4 border-white"
                                            style="width: 100px; height: 100px; font-size: 2rem;">
                                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                        </div>
                                    @endif
                                    <label for="avatar"
                                        class="position-absolute bottom-0 end-0 bg-primary shadow-lg rounded-circle p-2 cursor-pointer"
                                        style="width: 36px; height: 36px; display: flex; align-items: center; justify-content: center;">
                                        <i class="bi bi-camera-fill text-white small"></i>
                                    </label>
                                    <input type="file" name="avatar" id="avatar" class="d-none" accept="image/*">
                                </div>
                                <div>
                                    <h6 class="fw-bold mb-1">Profile Photo</h6>
                                    <p class="text-muted small mb-0">Upload a clear photo for your portal identity card.</p>
                                </div>
                            </div>

                            <div class="row g-4">
                                <div class="col-md-6">
                                    <label class="form-label tiny fw-bold text-muted text-uppercase">Full Name</label>
                                    <input type="text" name="name" class="form-control form-control-lg rounded-4 shadow-none border-light"
                                        value="{{ old('name', auth()->user()->name) }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label tiny fw-bold text-muted text-uppercase">Email Address</label>
                                    <input type="email" name="email" class="form-control form-control-lg rounded-4 shadow-none border-light"
                                        value="{{ old('email', auth()->user()->email) }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label tiny fw-bold text-muted text-uppercase">Phone Number</label>
                                    <input type="text" name="phone" class="form-control form-control-lg rounded-4 shadow-none border-light"
                                        value="{{ old('phone', auth()->user()->phone) }}" placeholder="+91 98765 43210">
                                </div>
                                <div class="col-12 text-end">
                                    <button type="submit" class="btn btn-primary btn-lg rounded-pill px-5 shadow-sm fw-bold">
                                        Save Profile
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                    <div class="card-header bg-white border-0 pt-4 px-4">
                        <h5 class="fw-bold mb-0">Password & Security</h5>
                    </div>
                    <div class="card-body p-4">
                        <form action="{{ route('profile.update') }}" method="POST">
                            @csrf
                            @method('patch')

                            <input type="hidden" name="name" value="{{ auth()->user()->name }}">
                            <input type="hidden" name="email" value="{{ auth()->user()->email }}">
                            <input type="hidden" name="phone" value="{{ auth()->user()->phone }}">

                            <div class="row g-4">
                                <div class="col-12">
                                    <label class="form-label tiny fw-bold text-muted text-uppercase">Current Password</label>
                                    <input type="password" name="current_password"
                                        class="form-control form-control-lg rounded-4 shadow-none border-light">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label tiny fw-bold text-muted text-uppercase">New Password</label>
                                    <input type="password" name="new_password"
                                        class="form-control form-control-lg rounded-4 shadow-none border-light">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label tiny fw-bold text-muted text-uppercase">Confirm Password</label>
                                    <input type="password" name="new_password_confirmation"
                                        class="form-control form-control-lg rounded-4 shadow-none border-light">
                                </div>
                                <div class="col-12 text-end">
                                    <button type="submit" class="btn btn-dark btn-lg rounded-pill px-5 shadow-sm fw-bold">
                                        Update Password
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-3">Current Account Snapshot</h5>
                        <div class="list-group list-group-flush rounded-4 overflow-hidden border">
                            <div class="list-group-item px-4 py-3">
                                <small class="text-muted tiny fw-bold text-uppercase d-block mb-1">Assigned {{ $isSport ? 'Batches' : 'Classes/Batches' }}</small>
                                <span class="fw-bold">{{ $teacher->batches()->count() }}</span>
                            </div>
                            <div class="list-group-item px-4 py-3">
                                <small class="text-muted tiny fw-bold text-uppercase d-block mb-1">Qualification</small>
                                <span class="fw-bold">{{ $teacher->qualification ?? 'Not added' }}</span>
                            </div>
                            <div class="list-group-item px-4 py-3">
                                <small class="text-muted tiny fw-bold text-uppercase d-block mb-1">{{ $isSport ? 'Specialization' : 'Subject Area' }}</small>
                                <span class="fw-bold">{{ $teacher->specialization ?? 'Not added' }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm rounded-4 bg-primary text-white">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-3"><i class="bi bi-headset me-2"></i> Support</h5>
                        <p class="small opacity-75 mb-4">Need changes beyond your own profile? Contact the school admin for role, batch, or portal preference updates.</p>
                        <a href="mailto:{{ auth()->user()->school->email ?? 'support@school.com' }}"
                            class="btn btn-white w-100 rounded-pill py-2 fw-bold text-primary shadow-sm border-0">
                            Contact Admin
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
