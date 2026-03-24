<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\Fee;
use App\Http\Requests\StoreFeePaymentRequest;
use App\Services\FeeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class FeePaymentController extends Controller
{
    public function __construct(private FeeService $feeService) {}

    public function create(Fee $fee)
    {
        return redirect()->route('school.payments.collect', ['student' => $fee->student_id, 'fee_id' => $fee->id]);
    }

    /**
     * Show unified fee collection hub for a student
     */
    public function collect(Request $request, ?\App\Models\Student $student = null)
    {
        $students = \App\Models\Student::with('user')->active()->get();

        $pendingFees = [];
        if ($student) {
            $pendingFees = Fee::where('student_id', $student->id)
                ->whereIn('status', ['pending', 'partial', 'overdue'])
                ->with('batch')
                ->get();
        }

        return view('school.payments.collect', compact('students', 'student', 'pendingFees'));
    }

    /**
     * Handle bulk fee payments
     */
    public function bulkStore(Request $request)
    {
        $schoolId = auth()->user()->school_id;

        $validated = $request->validate([
            'student_id' => ['required', Rule::exists('students', 'id')->where('school_id', $schoolId)],
            'payments' => 'required|array|min:1|max:200',
            'payments.*.fee_id' => ['required', 'distinct', Rule::exists('fees', 'id')->where('school_id', $schoolId)],
            'payments.*.amount' => 'required|numeric|min:0|max:99999999.99',
            'payment_method' => 'required|string|in:cash,bank_transfer,card,cheque,upi',
            'paid_at' => 'required|date|before_or_equal:today',
            'transaction_id' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
        ]);

        $feeIds = collect($validated['payments'])->pluck('fee_id')->all();
        $validFeeCount = Fee::whereIn('id', $feeIds)
            ->where('school_id', $schoolId)
            ->where('student_id', $validated['student_id'])
            ->count();

        if ($validFeeCount !== count($feeIds)) {
            return back()->withErrors([
                'payments' => 'One or more selected fees do not belong to the chosen student.',
            ])->withInput();
        }

        try {
            DB::beginTransaction();

            $lastInvoiceId = null;
            $paymentCount = 0;

            foreach ($validated['payments'] as $pData) {
                if ($pData['amount'] <= 0)
                    continue;
                $payment = $this->feeService->recordPayment([
                    'fee_id' => $pData['fee_id'],
                    'amount' => $pData['amount'],
                    'payment_method' => $validated['payment_method'],
                    'transaction_id' => $validated['transaction_id'],
                    'notes' => $validated['notes'],
                    'paid_at' => $validated['paid_at'],
                ]);

                $invoice = \App\Models\Invoice::where('fee_payment_id', $payment->id)->first();
                if ($invoice)
                    $lastInvoiceId = $invoice->id;

                $paymentCount++;
            }

            DB::commit();

            if ($paymentCount === 0) {
                return back()->with('error', 'No payment amounts were entered.');
            }

            return redirect()->route('school.students.show', $validated['student_id'])
                ->with('success', "Successfully recorded $paymentCount payments.")
                ->with('open_invoice_id', $lastInvoiceId);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error recording bulk payments: ' . $e->getMessage());
        }
    }

    public function store(StoreFeePaymentRequest $request)
    {
        try {
            $payment = $this->feeService->recordPayment($request->validated());

            // Get the newly generated invoice linked to this payment
            $invoice = \App\Models\Invoice::where('fee_payment_id', $payment->id)->first();

            return redirect()->route('school.fees.show', $request->fee_id)
                ->with('success', 'Payment recorded successfully.')
                ->with('open_invoice_id', $invoice ? $invoice->id : null);
        } catch (\Exception $e) {
            return back()->with('error', 'Error recording payment: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $this->feeService->deletePayment($id);
            return back()->with('success', 'Payment deleted and fee status reverted.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error deleting payment: ' . $e->getMessage());
        }
    }
}
