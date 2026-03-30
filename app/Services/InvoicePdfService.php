<?php

namespace App\Services;

use App\Models\Invoice;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoicePdfService
{
    /**
     * Build a configured Dompdf instance with FreeSans font registered.
     */
    public function build(Invoice $invoice): \Barryvdh\DomPDF\PDF
    {
        $pdf = Pdf::loadView('school.invoices.pdf', compact('invoice'));

        $fontPath = public_path('fonts/FreeSans');

        $pdf->getDomPDF()
            ->getFontMetrics()
            ->setFontFamily('FreeSans', [
                'normal' => $fontPath,
                'bold' => $fontPath,
                'italic' => $fontPath,
                'bold_italic' => $fontPath,
            ]);

        return $pdf;
    }
}
