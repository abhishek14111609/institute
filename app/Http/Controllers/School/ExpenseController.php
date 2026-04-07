<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\CategoryType;
use App\Models\Expense;
use App\Http\Requests\StoreExpenseRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $schoolId = (int) auth()->user()->school_id;
        $query = Expense::query();

        /** @var string|null $category */
        $category = $request->input('category');
        if ($category) {
            $query->byCategory($category);
        }

        /** @var string|null $startDate */
        $startDate = $request->input('start_date');
        /** @var string|null $endDate */
        $endDate = $request->input('end_date');
        if ($startDate && $endDate) {
            $query->byDateRange($startDate, $endDate);
        }

        $expenses = $query->latest('expense_date')->paginate(15);
        $categories = CategoryType::query()
            ->forSchoolModule($schoolId, CategoryType::MODULE_EXPENSE)
            ->active()
            ->orderBy('name')
            ->get();

        if ($categories->isEmpty()) {
            $legacyCategories = Expense::query()
                ->where('school_id', $schoolId)
                ->whereNotNull('category')
                ->distinct()
                ->pluck('category')
                ->filter();

            $categories = $legacyCategories->map(fn($name) => (object) ['name' => $name]);
        }

        return view('school.expenses.index', compact('expenses', 'categories'));
    }

    public function create()
    {
        $schoolId = (int) auth()->user()->school_id;
        $categories = CategoryType::query()
            ->forSchoolModule($schoolId, CategoryType::MODULE_EXPENSE)
            ->active()
            ->orderBy('name')
            ->get();

        return view('school.expenses.create', compact('categories'));
    }

    public function store(StoreExpenseRequest $request)
    {
        try {
            $data = $request->validated();
            $data['created_by'] = auth()->id();
            $data['category'] = $this->resolveCategoryName(
                (int) auth()->user()->school_id,
                CategoryType::MODULE_EXPENSE,
                $data['category'] ?? null,
                $request->input('new_category')
            );
            unset($data['new_category']);

            if ($request->hasFile('receipt')) {
                $data['receipt'] = $request->file('receipt')->store('expenses/receipts', 'public');
            }

            Expense::create($data);

            return redirect()->route('school.expenses.index')
                ->with('success', 'Expense created successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error creating expense: ' . $e->getMessage());
        }
    }

    public function show(Expense $expense)
    {
        return view('school.expenses.show', compact('expense'));
    }

    public function edit(Expense $expense)
    {
        return view('school.expenses.edit', compact('expense'));
    }

    public function update(Request $request, Expense $expense)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'amount' => 'required|numeric|min:0.01|max:99999999.99',
            'category' => 'nullable|string|max:100|not_in:__new__|required_without:new_category',
            'new_category' => 'nullable|string|max:100',
            'expense_date' => 'required|date|before_or_equal:today',
            'receipt' => 'nullable|file|mimes:pdf,jpeg,png,jpg|max:5120',
        ]);

        $validated['category'] = $this->resolveCategoryName(
            (int) auth()->user()->school_id,
            CategoryType::MODULE_EXPENSE,
            $validated['category'] ?? null,
            $request->input('new_category')
        );
        unset($validated['new_category']);

        if ($request->hasFile('receipt')) {
            $validated['receipt'] = $request->file('receipt')->store('expenses/receipts', 'public');
        }

        $expense->update($validated);

        return redirect()->route('school.expenses.index')
            ->with('success', 'Expense updated successfully.');
    }

    private function resolveCategoryName(int $schoolId, string $module, ?string $category, ?string $newCategory): string
    {
        $selected = trim((string) ($category ?? ''));
        $custom = trim((string) ($newCategory ?? ''));

        $name = $custom !== '' ? $custom : $selected;
        if ($name === '') {
            abort(422, 'Category is required.');
        }

        $slug = Str::slug($name);
        if ($slug === '') {
            $slug = Str::slug($name . '-' . uniqid());
        }

        CategoryType::query()->updateOrCreate(
            [
                'school_id' => $schoolId,
                'module' => $module,
                'slug' => $slug,
            ],
            [
                'name' => $name,
                'is_active' => true,
            ]
        );

        return $name;
    }

    public function destroy(Expense $expense)
    {
        $expense->delete();

        return redirect()->route('school.expenses.index')
            ->with('success', 'Expense deleted successfully.');
    }
}
