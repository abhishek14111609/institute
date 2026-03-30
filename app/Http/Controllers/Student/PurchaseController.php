<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;

class PurchaseController extends Controller
{
    public function index()
    {
        $student = auth()->user()->student;

        $purchases = $student->inventorySales()
            ->with(['item', 'invoice'])
            ->latest()
            ->get();

        $stats = [
            'total_orders' => $purchases->count(),
            'total_items' => $purchases->sum('quantity'),
            'total_spent' => $purchases->sum('total_amount'),
        ];

        return view('student.purchases.index', compact('purchases', 'stats'));
    }
}
