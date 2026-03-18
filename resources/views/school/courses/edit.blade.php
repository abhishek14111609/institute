@extends('layouts.app')

@section('title', 'Edit Course: ' . $course->name)

@section('sidebar')
    @include('school.sidebar')
@endsection

@section('content')
    @php($isSport = auth()->user()->school->institute_type === 'sport')
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ $isSport ? __('Edit Sport') : __('Edit Course') }}: {{ $course->name }}</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('school.courses.update', $course) }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="name"
                                class="form-label">{{ $isSport ? __('Sport Name') : __('Course Name') }}</label>
                            <input id="name" type="text" class="form-control @error('name') is-invalid @enderror"
                                name="name" value="{{ old('name', $course->name) }}" required autofocus>
                            @error('name')
                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="code"
                                class="form-label">{{ $isSport ? __('Sport Code') : __('Course Code') }}</label>
                            @if ($isSport)
                                <div class="input-group">
                                    <input id="code" type="text"
                                        class="form-control @error('code') is-invalid @enderror" name="code"
                                        value="{{ old('code', $course->code ?: $suggestedCode ?? '') }}" readonly>
                                    <button class="btn btn-outline-primary" type="button"
                                        id="generateCodeBtn">Generate</button>
                                </div>
                            @else
                                <input id="code" type="text"
                                    class="form-control @error('code') is-invalid @enderror" name="code"
                                    value="{{ old('code', $course->code) }}">
                            @endif
                            @if ($isSport)
                                <small class="text-muted d-block mt-1">Generated format: {{ $prefix ?? 'INS' }}001,
                                    {{ $prefix ?? 'INS' }}002...</small>
                            @endif
                            @error('code')
                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">{{ __('Description') }}</label>
                            <textarea id="description" class="form-control @error('description') is-invalid @enderror" name="description"
                                rows="3">{{ old('description', $course->description) }}</textarea>
                            @error('description')
                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                            @enderror
                        </div>

                        <div class="mb-3 form-check">
                            <input type="hidden" name="is_active" value="0">
                            <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1"
                                {{ old('is_active', $course->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">{{ __('Active') }}</label>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('school.courses.index') }}" class="btn btn-secondary">Cancel</a>
                            <button type="submit"
                                class="btn btn-primary">{{ $isSport ? __('Update Sport') : __('Update Course') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @if ($isSport)
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const codeInput = document.getElementById('code');
                const generateBtn = document.getElementById('generateCodeBtn');
                const codePrefix = @json($prefix ?? 'INS');
                let nextSequence = Number(@json($nextSequence ?? 1));

                function generateSportCode(sequence) {
                    return `${codePrefix}${String(sequence).padStart(3, '0')}`;
                }

                function syncSportCode() {
                    const generated = generateSportCode(nextSequence);
                    codeInput.value = generated;
                }

                if (!codeInput.value) {
                    syncSportCode();
                }

                generateBtn.addEventListener('click', function() {
                    nextSequence += 1;
                    syncSportCode();
                });
            });
        </script>
    @endif
@endsection
