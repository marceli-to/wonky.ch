<?php

namespace App\Actions\Order;

use App\Models\Order;

class GeneratePaymentReference
{
    /**
     * Generate a unique payment reference for Payrexx.
     * Format: WONKY-XXXXXX (random alphanumeric)
     */
    public function execute(): string
    {
        do {
            $reference = 'WONKY-'.$this->generateCode(6);
        } while (Order::where('payment_reference', $reference)->exists());

        return $reference;
    }

    private function generateCode(int $length): string
    {
        // Excludes 0, O, 1, I, L to avoid confusion
        $chars = 'ABCDEFGHJKMNPQRSTUVWXYZ23456789';
        $code = '';

        for ($i = 0; $i < $length; $i++) {
            $code .= $chars[random_int(0, strlen($chars) - 1)];
        }

        return $code;
    }
}
