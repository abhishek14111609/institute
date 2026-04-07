@extends('layouts.app')

@section('title', 'Add Expense')

@section('sidebar')
    @include('school.sidebar')
@endsection

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Add New Expense</h2>
            <a href="{{ route('school.expenses.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Back
            </a>
        </div>

        <div class="card">
            <div class="card-body">
                <form action="{{ route('school.expenses.store') }}" method="POST">
                    @csrf

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('title') is-invalid @enderror"
                                    id="title" name="title" value="{{ old('title') }}" required>
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="category" class="form-label">Category <span class="text-danger">*</span></label>
                                <select class="form-select @error('category') is-invalid @enderror" id="category"
                                    name="category" required>
                                    <option value="">Select Category</option>
                                    @foreach ($categories ?? collect() as $categoryItem)
                                        <option value="{{ $categoryItem->name }}"
                                            {{ old('category') === $categoryItem->name ? 'selected' : '' }}>
                                            {{ $categoryItem->name }}
                                        </option>
                                    @endforeach
                                    <option value="__new__" {{ old('category') === '__new__' ? 'selected' : '' }}>+ Add New
                                        Category</option>
                                </select>
                                @error('category')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="mt-2 {{ old('category') === '__new__' || old('new_category') ? '' : 'd-none' }}"
                                    id="new_category_wrap">
                                    <input type="text" class="form-control @error('new_category') is-invalid @enderror"
                                        id="new_category" name="new_category" value="{{ old('new_category') }}"
                                        placeholder="Enter new expense category">
                                    @error('new_category')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="amount" class="form-label">Amount (₹) <span
                                        class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('amount') is-invalid @enderror"
                                    id="amount" name="amount" value="{{ old('amount') }}" step="0.01" min="0"
                                    required>
                                @error('amount')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="expense_date" class="form-label">Expense Date <span
                                        class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('expense_date') is-invalid @enderror"
                                    id="expense_date" name="expense_date" value="{{ old('expense_date', date('Y-m-d')) }}"
                                    required>
                                @error('expense_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description"
                            rows="3">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle"></i> Add Expense
                        </button>
                        <a href="{{ route('school.expenses.index') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const categorySelect = document.getElementById('category');
            const newCategoryWrap = document.getElementById('new_category_wrap');
            const newCategoryInput = document.getElementById('new_category');

            if (!categorySelect || !newCategoryWrap || !newCategoryInput) {
                return;
            }

            const toggleNewCategory = () => {
                const isNew = categorySelect.value === '__new__';
                newCategoryWrap.classList.toggle('d-none', !isNew);
                if (isNew) {
                    newCategoryInput.focus();
                    categorySelect.removeAttribute('required');
                    newCategoryInput.setAttribute('required', 'required');
                } else {
                    newCategoryInput.removeAttribute('required');
                    categorySelect.setAttribute('required', 'required');
                }
            };

            categorySelect.addEventListener('change', toggleNewCategory);
            toggleNewCategory();
        });
    </script>
@endsection
