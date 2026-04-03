@extends('layouts.app')

@section('title', __('Session Expired'))

@section('content')
    <div class="container-fluid d-flex align-items-center justify-content-center" style="min-height: 80vh;">
        <div class="row w-100">
            <div class="col-lg-6 mx-auto">
                <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                    <div class="card-body p-5 text-center">
                        <!-- Error Icon -->
                        <div class="mb-4">
                            <div class="display-1 text-warning mb-3">
                                <i class="bi bi-exclamation-triangle-fill"></i>
                            </div>
                        </div>

                        <!-- Error Title -->
                        <h1 class="fw-bold text-dark mb-2">Session Expired</h1>
                        <p class="text-muted lead mb-4">Your session has expired due to inactivity or expired CSRF token.
                        </p>

                        <!-- Error Description -->
                        <div class="alert alert-warning border-0 bg-warning bg-opacity-10 text-warning-emphasis rounded-3 mb-4"
                            role="alert">
                            <i class="bi bi-info-circle-fill me-2"></i>
                            <span class="fw-semibold">Error Code: 419</span> - Page Expired
                        </div>

                        <!-- Explanation -->
                        <div class="bg-light rounded-3 p-4 mb-4 text-start">
                            <h6 class="fw-bold text-dark mb-2">What happened?</h6>
                            <ul class="mb-0 small text-muted">
                                <li class="mb-2">Your session has timed out due to inactivity</li>
                                <li class="mb-2">The security token (CSRF) has expired</li>
                                <li>Please refresh your browser and try again</li>
                            </ul>
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-grid gap-2 d-sm-flex justify-content-sm-center">
                            <a href="{{ url('/') }}" class="btn btn-primary btn-lg rounded-pill px-4">
                                <i class="bi bi-house-fill me-2"></i>Back to Home
                            </a>
                            <button class="btn btn-outline-secondary btn-lg rounded-pill px-4"
                                onclick="window.history.back()">
                                <i class="bi bi-arrow-left me-2"></i>Go Back
                            </button>
                        </div>

                        <!-- Footer Note -->
                        <hr class="my-4 opacity-25">
                        <p class="small text-muted mb-0">
                            If you continue experiencing this issue, please contact support or <a
                                href="{{ route('login') }}" class="text-decoration-none fw-semibold">login again</a>.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }

        .card {
            position: relative;
            top: -50px;
        }

        @media (max-width: 576px) {
            .display-1 {
                font-size: 3rem;
            }
        }
    </style>
@endsection
