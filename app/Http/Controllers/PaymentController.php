<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class PaymentController extends Controller
{
    public function store(Request $request, Order $order): RedirectResponse
    {
        $data = $request->validate([
            'amount' => ['required', 'numeric', 'gt:0', 'decimal:0,2'],
            'payment_method' => ['required', 'in:cash,card,ewallet,bank_transfer'],
        ]);

        DB::transaction(function () use ($order, $data) {
            $lockedOrder = Order::whereKey($order->id)->lockForUpdate()->firstOrFail();
            $paidCents = (int) round((float) $lockedOrder->payments()->sum('amount') * 100);
            $amountCents = (int) round((float) $data['amount'] * 100);
            $totalCents = (int) round((float) $lockedOrder->total_amount * 100);
            if ($paidCents + $amountCents > $totalCents) {
                throw ValidationException::withMessages(['amount' => 'Payments cannot exceed the order total.']);
            }

            $lockedOrder->payments()->create([
                'payment_reference' => 'PAY-'.Str::upper(Str::random(16)),
                'payment_method' => $data['payment_method'],
                'amount' => number_format($amountCents / 100, 2, '.', ''),
                'paid_at' => now(),
            ]);
        });

        return redirect()->route('orders.index')->with('status', 'Payment recorded.');
    }
}
