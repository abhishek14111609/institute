<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\InventoryItem;
use App\Models\Invoice;
use App\Models\InventorySale;
use App\Models\Level;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class InventoryController extends Controller
{
    /**
     * Display a listing of the physical inventory.
     */
    public function index(Request $request)
    {
        $schoolId = $this->currentSchoolId();
        
        $query = InventoryItem::query()
            ->where('school_id', '=', $schoolId, 'and')
            ->with(['course', 'level']);

        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('category', 'LIKE', "%{$searchTerm}%");
            });
        }

        if ($request->filled('category') && $request->category !== 'All') {
            $query->where('category', $request->category);
        }

        $items = $query->get();
            
        $recentSales = InventorySale::query()
            ->where('school_id', '=', $schoolId, 'and')
            ->with(['student.user', 'item', 'invoice'])
            ->latest()
            ->take(10)
            ->get();
            
        $students = Student::query()
            ->where('school_id', '=', $schoolId, 'and')
            ->where('is_active', '=', 1, 'and')
            ->with('user')
            ->get();

        return view('school.inventory.index', compact('items', 'recentSales', 'students'));
    }

    /**
     * Show the form for creating a new item.
     */
    public function create()
    {
        return redirect()
            ->route('school.inventory.index')
            ->with('success', 'Use the "Add New Item" button to register stock.');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $schoolId = $this->currentSchoolId();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'alert_quantity' => 'nullable|integer|min:0',
            'course_id' => [
                'nullable',
                Rule::exists('courses', 'id')->where('school_id', $schoolId),
            ],
            'level_id' => [
                'nullable',
                Rule::exists('levels', 'id')->where('school_id', $schoolId),
            ],
        ]);

        InventoryItem::create([
            'school_id' => $schoolId,
            'course_id' => $validated['course_id'] ?? null,
            'level_id' => $validated['level_id'] ?? null,
            'name' => $validated['name'],
            'category' => $validated['category'],
            'price' => $validated['price'],
            'stock_quantity' => $validated['stock_quantity'],
            'alert_quantity' => $validated['alert_quantity'] ?? 5,
            'status' => 1,
        ]);

        return redirect()->route('school.inventory.index')->with('success', 'Item added to inventory.');
    }

    /**
     * Sell or issue an item to a student.
     */
    public function issueItem(Request $request)
    {
        $schoolId = $this->currentSchoolId();

        $validated = $request->validate([
            'student_id' => [
                'required',
                Rule::exists('students', 'id')->where('school_id', $schoolId),
            ],
            'item_id' => [
                'required',
                Rule::exists('inventory_items', 'id')->where('school_id', $schoolId),
            ],
            'quantity' => 'required|integer|min:1',
        ]);

        $item = InventoryItem::query()
            ->where('school_id', '=', $schoolId, 'and')
            ->findOrFail($validated['item_id']);

        if ($item->stock_quantity < $validated['quantity']) {
            return back()->with('error', 'Insufficient stock available.');
        }

        $invoiceId = DB::transaction(function () use ($validated, $schoolId, $item) {
            $totalAmount = $item->price * $validated['quantity'];

            $sale = InventorySale::create([
                'school_id' => $schoolId,
                'student_id' => $validated['student_id'],
                'item_id' => $validated['item_id'],
                'quantity' => $validated['quantity'],
                'unit_price' => $item->price,
                'total_amount' => $totalAmount,
                'payment_status' => 'paid',
            ]);

            $invoice = Invoice::create([
                'school_id' => $schoolId,
                'student_id' => $validated['student_id'],
                'fee_id' => null,
                'fee_payment_id' => null,
                'invoice_number' => Invoice::generateInvoiceNumber($schoolId),
                'invoice_date' => now(),
                'amount' => $totalAmount,
            ]);

            $sale->update(['invoice_id' => $invoice->id]);
            $item->decrement('stock_quantity', $validated['quantity'], []);

            return $invoice->id;
        });

        return redirect()
            ->route('school.inventory.index')
            ->with('success', 'Cash sale recorded and invoice generated successfully.')
            ->with('open_invoice_id', $invoiceId);
    }

    /**
     * Display a single inventory item.
     */
    public function show(InventoryItem $inventory)
    {
        $this->ensureInventoryBelongsToCurrentSchool($inventory);

        return redirect()->route('school.inventory.edit', $inventory);
    }

    /**
     * Show edit form for an inventory item.
     */
    public function edit(InventoryItem $inventory)
    {
        $schoolId = $this->currentSchoolId();

        $this->ensureInventoryBelongsToCurrentSchool($inventory);

        $courses = Course::query()
            ->where('school_id', '=', $schoolId, 'and')
            ->get();
        $levels = Level::query()
            ->where('school_id', '=', $schoolId, 'and')
            ->get();
        
        return view('school.inventory.edit', compact('inventory', 'courses', 'levels'));
    }

    /**
     * Update an inventory item.
     */
    public function update(Request $request, InventoryItem $inventory)
    {
        $schoolId = $this->currentSchoolId();

        $this->ensureInventoryBelongsToCurrentSchool($inventory);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'alert_quantity' => 'nullable|integer|min:0',
            'course_id' => [
                'nullable',
                Rule::exists('courses', 'id')->where('school_id', $schoolId),
            ],
            'level_id' => [
                'nullable',
                Rule::exists('levels', 'id')->where('school_id', $schoolId),
            ],
        ]);

        $inventory->update([
            'name' => $validated['name'],
            'category' => $validated['category'],
            'price' => $validated['price'],
            'stock_quantity' => $validated['stock_quantity'],
            'alert_quantity' => $validated['alert_quantity'] ?? 5,
            'course_id' => $validated['course_id'] ?? null,
            'level_id' => $validated['level_id'] ?? null,
        ]);

        return redirect()->route('school.inventory.index')->with('success', 'Stock updated.');
    }

    /**
     * Remove an item from stock records.
     */
    public function destroy(InventoryItem $inventory)
    {
        $this->ensureInventoryBelongsToCurrentSchool($inventory);

        InventoryItem::destroy($inventory->getKey());
        return back()->with('success', 'Item removed from records.');
    }

    protected function currentSchoolId(): int
    {
        return (int) $this->currentUser()->school_id;
    }

    protected function ensureInventoryBelongsToCurrentSchool(InventoryItem $inventory): void
    {
        abort_unless($inventory->school_id === $this->currentSchoolId(), 403);
    }

    protected function currentUser(): User
    {
        /** @var User $user */
        $user = auth()->user();

        return $user;
    }
}
