<?php

namespace App\Actions\Order;

use App\Models\Order;
use Illuminate\Support\Facades\Storage;
use Spatie\LaravelPdf\Facades\Pdf;

class GenerateInvoicePdf
{
    public function execute(Order $order): string
    {
        $filename = "invoice-{$order->order_number}.pdf";
        $path = "invoices/{$filename}";

        // Ensure the invoices directory exists
        Storage::disk('local')->makeDirectory('invoices');

        $pdf = Pdf::view('pdf.invoice', ['order' => $order])
            ->format('a4')
            ->headerView('pdf.partials.logo')
            ->footerView('pdf.partials.page-number')
            ->margins(32, 12, 8, 6);

        if (app()->environment('production') || app()->bound('force_lambda_pdf')) {
            $pdf->onLambda();
        }

        $pdf->save(Storage::disk('local')->path($path));

        return $path;
    }
}
