<x-app-layout>
    <x-slot name="header"><div class="flex items-center justify-between"><h2 class="text-xl font-semibold leading-tight text-gray-800">Orders</h2><a href="{{ route('orders.create') }}" class="rounded bg-amber-800 px-4 py-2 text-white">New order</a></div></x-slot>
    <div class="py-8"><div class="mx-auto max-w-7xl space-y-4 px-4 sm:px-6 lg:px-8">
        @if (session('status')) <div class="rounded bg-green-100 p-3 text-green-800">{{ session('status') }}</div> @endif
        @if ($errors->any()) <div class="rounded bg-red-100 p-3 text-red-800">{{ $errors->first() }}</div> @endif
        <div class="overflow-x-auto rounded-lg bg-white shadow"><table class="min-w-full divide-y divide-gray-200 text-left text-sm">
            <thead class="bg-gray-50 text-xs uppercase text-gray-600"><tr><th class="px-5 py-3">Order number</th><th class="px-5 py-3">Customer</th><th class="px-5 py-3">Staff member</th><th class="px-5 py-3">Total</th><th class="px-5 py-3">Status</th><th class="px-5 py-3">Payments</th></tr></thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($orders as $order)
                    <tr><td class="px-5 py-4 font-medium">{{ $order->order_number }}<div class="text-xs text-gray-500">{{ $order->order_date->format('M j, Y g:i A') }}</div></td><td class="px-5 py-4">{{ $order->customer->name }}</td><td class="px-5 py-4">{{ $order->staff->name }}</td><td class="px-5 py-4">₱{{ number_format((float) $order->total_amount, 2) }}</td><td class="px-5 py-4"><span class="rounded-full bg-amber-100 px-2 py-1 text-xs capitalize text-amber-900">{{ $order->status }}</span></td><td class="px-5 py-4">
                        <div class="mb-2 text-xs text-gray-600">Paid ₱{{ number_format((float) $order->payments->sum('amount'), 2) }}</div>
                        @if ($order->payments->sum('amount') < $order->total_amount)
                            <form method="POST" action="{{ route('orders.payments.store', $order) }}" class="flex flex-wrap gap-2">@csrf
                                <input name="amount" type="number" min="0.01" max="{{ number_format((float) $order->total_amount - (float) $order->payments->sum('amount'), 2, '.', '') }}" step="0.01" placeholder="Amount" class="w-24 rounded border-gray-300 text-sm" required>
                                <select name="payment_method" class="rounded border-gray-300 text-sm"><option value="cash">Cash</option><option value="card">Card</option><option value="ewallet">E-wallet</option><option value="bank_transfer">Bank</option></select>
                                <button class="rounded bg-gray-800 px-3 py-2 text-xs text-white">Record</button>
                            </form>
                        @endif
                    </td></tr>
                @empty <tr><td colspan="6" class="px-5 py-8 text-center text-gray-500">No orders yet. Create your first order.</td></tr> @endforelse
            </tbody>
        </table></div>
        <div>{{ $orders->links() }}</div>
    </div></div>
</x-app-layout>
