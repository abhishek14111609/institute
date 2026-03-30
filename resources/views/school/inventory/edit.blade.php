@extends('layouts.app')

@section('title', 'Manage Stock Item')

@section('sidebar')
    @include('school.sidebar')
@endsection

@section('content')
    <div class="container-fluid py-4 font-outfit">
        <div class="row m-0">
            <div class="col-xl-6 mx-auto">
                <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                    <div class="card-header bg-primary text-white border-0 py-4 px-4">
                        <h4 class="fw-bold mb-0"><i class="bi bi-pencil-square me-2"></i> Update Stock: {{ $inventory->name }}</h4>
                        <p class="small opacity-75 mb-0">Modify details or adjust stock levels.</p>
                    </div>
                    <form action="{{ route('school.inventory.update', $inventory) }}" method="POST">
                        @csrf @method('PUT')
                        <div class="card-body p-4">
                            <div class="mb-3">
                                <label class="form-label small fw-bold text-muted">ITEM NAME</label>
                                <input type="text" name="name" class="form-control" value="{{ $inventory->name }}" required>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label small fw-bold text-muted">CATEGORY</label>
                                    <select name="category" class="form-select" required>
                                        <option value="Kit" {{ $inventory->category == 'Kit' ? 'selected' : '' }}>Kit / Sport Gear</option>
                                        <option value="Book" {{ $inventory->category == 'Book' ? 'selected' : '' }}>Educational Book</option>
                                        <option value="Uniform" {{ $inventory->category == 'Uniform' ? 'selected' : '' }}>Uniform / Dress</option>
                                        <option value="Other" {{ $inventory->category == 'Other' ? 'selected' : '' }}>Other Equipment</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label small fw-bold text-muted">UNIT PRICE (₹)</label>
                                    <input type="number" name="price" class="form-control" value="{{ $inventory->price }}" step="0.01" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label small fw-bold text-muted">CURRENT STOCK</label>
                                    <input type="number" name="stock_quantity" class="form-control" value="{{ $inventory->stock_quantity }}" required>
                                    <small class="text-muted small d-block mt-1">Manual stock override.</small>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label small fw-bold text-muted">LOW STOCK THRESHOLD</label>
                                    <input type="number" name="alert_quantity" class="form-control" value="{{ $inventory->alert_quantity }}">
                                </div>
                            </div>
                            
                            <div class="mb-0">
                                <label class="form-label small fw-bold text-muted">ATTACH TO COURSE / CLASS (OPTIONAL)</label>
                                <select name="course_id" class="form-select">
                                    <option value="">— Not specific —</option>
                                    @foreach($courses as $course)
                                        <option value="{{ $course->id }}" {{ $inventory->course_id == $course->id ? 'selected' : '' }}>{{ $course->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="card-footer border-0 p-4 pt-0 bg-white">
                            <hr class="mb-4">
                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('school.inventory.index') }}" class="btn btn-light rounded-pill px-4">Cancel</a>
                                <button type="submit" class="btn btn-primary rounded-pill px-5 shadow">Update Records</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <style>
        .font-outfit { font-family: 'Outfit', sans-serif; }
    </style>
@endsection
