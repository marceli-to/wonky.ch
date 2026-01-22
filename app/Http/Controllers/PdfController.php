<?php

namespace App\Http\Controllers;

use App\Actions\Pdf\Create as CreatePdfAction;
use App\Models\Order;

class PdfController extends Controller
{
    /**
     * Generate and download invoice PDF for a specific order.
     */
    public function generateInvoice(Order $order)
    {
        if (! $order->isPaid()) {
            abort(403, 'Invoice can only be generated for paid orders.');
        }

        $pdf = (new CreatePdfAction)->execute($order);

        return $pdf->download('invoice-'.$order->order_number.'.pdf');
    }
}
