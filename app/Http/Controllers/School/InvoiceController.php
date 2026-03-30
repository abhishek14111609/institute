<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Services\InvoicePdfService;

class InvoiceController extends Controller
{
    public function __construct(private InvoicePdfService $invoicePdfService)
    {
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $invoices = Invoice::with([
            'student.user',
            'student.batch.class',
            'student.batches.subject',
            'fee',
            'feePayment',
            'inventorySale.item',
        ])
            ->latest()
            ->paginate(15);

        return view('school.invoices.index', compact('invoices'));
    }

    /**
     * Download invoice as PDF.
     */
    public function download(Invoice $invoice)
    {
        if ($invoice->school_id !== auth()->user()->school_id) {
            abort(403);
        }

        $pdf = $this->invoicePdfService->build($invoice);

        return $pdf->download("Invoice-{$invoice->invoice_number}.pdf");
    }

    /**
     * View invoice as PDF in browser.
     */
    public function stream(Invoice $invoice)
    {
        if ($invoice->school_id !== auth()->user()->school_id) {
            abort(403);
        }

        $pdf = $this->invoicePdfService->build($invoice);

        return $pdf->stream("Invoice-{$invoice->invoice_number}.pdf");
    }
}
