<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Services\InvoicePdfService;

class InvoiceController extends Controller
{
    public function __construct(private InvoicePdfService $invoicePdfService)
    {
    }

    public function download(Invoice $invoice)
    {
        abort_unless($invoice->student_id === auth()->user()->student?->id, 403);

        $pdf = $this->invoicePdfService->build($invoice);

        return $pdf->download("Invoice-{$invoice->invoice_number}.pdf");
    }

    public function stream(Invoice $invoice)
    {
        abort_unless($invoice->student_id === auth()->user()->student?->id, 403);

        $pdf = $this->invoicePdfService->build($invoice);

        return $pdf->stream("Invoice-{$invoice->invoice_number}.pdf");
    }
}
