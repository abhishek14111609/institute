<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class FeeController extends Controller
{
    public function index()
    {
        $student = auth()->user()->student;
        $fees = $student->fees()->with('payments')->latest()->get();

        $stats = [
            'total_due' => $fees->sum(fn($fee) => $fee->total_amount + $fee->late_fee - $fee->discount),
            'total_paid' => $fees->sum('paid_amount'),
            'total_remaining' => $fees->sum(fn($fee) => max(0, $fee->remaining_amount)),
            'payment_progress' => $fees->sum(fn($fee) => $fee->total_amount + $fee->late_fee - $fee->discount) > 0
                ? ($fees->sum('paid_amount') / $fees->sum(fn($fee) => $fee->total_amount + $fee->late_fee - $fee->discount)) * 100
                : 0
        ];

        return view('student.fees-index', compact('fees', 'stats'));
    }

    public function show($feeId)
    {
        $student = auth()->user()->student;
        $fee = $student->fees()->with(['payments', 'invoices'])->findOrFail($feeId);

        return view('student.fee-details', compact('fee'));
    }
}
