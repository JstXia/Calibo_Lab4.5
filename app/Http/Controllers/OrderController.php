<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Illuminate\Validation\ValidationException;

class OrderController extends Controller
{
    public function index(): View
    {
        return view('orders.index', ['orders' => Order::with(['customer', 'staff', 'payments'])->latest('order_date')->paginate(15)]);
    }

    public function create(): View
    {
        return view('orders.create', [
            'customers' => Customer::orderBy('name')->get(),
            'products' => Product::where('is_available', true)->where('stock_quantity', '>', 0)->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'customer_id' => ['required', 'exists:customers,id'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'distinct', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
        ]);

        $order = DB::transaction(function () use ($data, $request) {
            $order = Order::create([
                'order_number' => 'CAF-'.now()->format('YmdHis').'-'.strtoupper(bin2hex(random_bytes(2))),
                'customer_id' => $data['customer_id'],
                'user_id' => $request->user()->id,
                'order_date' => now(),
                'status' => 'pending',
                'total_amount' => 0,
            ]);

            $totalCents = 0;
            foreach ($data['items'] as $line) {
                $product = Product::whereKey($line['product_id'])->lockForUpdate()->firstOrFail();
                $quantity = (int) $line['quantity'];
                if (!$product->is_available || $product->stock_quantity < $quantity) {
                    throw ValidationException::withMessages(['items' => "Insufficient stock for {$product->name}."]);
                }

                $priceCents = (int) round(((float) $product->price) * 100);
                $subtotalCents = $priceCents * $quantity;
                $totalCents += $subtotalCents;
                $order->items()->create([
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'unit_price' => number_format($priceCents / 100, 2, '.', ''),
                    'subtotal' => number_format($subtotalCents / 100, 2, '.', ''),
                ]);
                $product->decrement('stock_quantity', $quantity);
            }

            $order->update(['total_amount' => number_format($totalCents / 100, 2, '.', '')]);
            return $order;
        });

        return redirect()->route('orders.index')->with('status', "Order {$order->order_number} was created.");
    }
}
