@extends('layouts.app')

@section('title', auth()->user()->school->institute_type === 'sport' ? 'Add Sport' : 'Create Course')

@section('sidebar')
    @include('school.sidebar')
@endsection

@section('content')
    @php($isSport = auth()->user()->school->institute_type === 'sport')
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    {{ $isSport ? __('Add New Sport') : __('Create New Course') }}
                </div>

                <div class="card-body">
                    <form method="POST" action="{{ route('school.courses.store') }}">
                        @csrf
                        <div class="mb-3">
                            <label for="name"
                                class="form-label">{{ $isSport ? __('Sport Name') : __('Course Name') }}</label>
                            <input id="name" type="text" class="form-control @error('name') is-invalid @enderror"
                                name="name" value="{{ old('name') }}" required autofocus
                                placeholder="{{ $isSport ? 'e.g. Cricket Academy' : 'e.g. High School Science Stream' }}">
                            @error('name')
                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="code" class="form-label">{{ $isSport ? __('Sport Code') : __('Course Code') }}
                                <small class="text-muted">(Optional)</small></label>
                            @if ($isSport)
                                <div class="input-group">
                                    <input id="code" type="text"
                                        class="form-control @error('code') is-invalid @enderror" name="code"
                                        value="{{ old('code', $suggestedCode ?? '') }}" placeholder="Auto-generated"
                                        readonly>
                                    <button class="btn btn-outline-primary" type="button"
                                        id="generateCodeBtn">Generate</button>
                                </div>
                            @else
                                <input id="code" type="text"
                                    class="form-control @error('code') is-invalid @enderror" name="code"
                                    value="{{ old('code') }}" placeholder="e.g. HS-SCI">
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
                                rows="3">{{ old('description') }}</textarea>
                            @error('description')
                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                            @enderror
                        </div>

                        <div class="mb-3 form-check">
                            <input type="hidden" name="is_active" value="0">
                            <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1"
                                {{ old('is_active', 1) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">{{ __('Active') }}</label>
                        </div>

                        <button type="submit"
                            class="btn btn-primary">{{ $isSport ? __('Add Sport') : __('Create Course') }}</button>
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

                syncSportCode();
                generateBtn.addEventListener('click', function() {
                    nextSequence += 1;
                    syncSportCode();
                });
            });
        </script>
    @endif
@endsection
