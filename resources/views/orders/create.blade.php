<x-app-layout>
    <x-slot name="header"><h2 class="text-xl font-semibold leading-tight text-gray-800">Create order</h2></x-slot>
    <div class="py-8"><div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8"><div class="rounded-lg bg-white p-6 shadow">
        @if ($errors->any()) <div class="mb-4 rounded bg-red-100 p-3 text-red-800">{{ $errors->first() }}</div> @endif
        <form method="POST" action="{{ route('orders.store') }}" class="space-y-6">@csrf
            <div><label class="mb-1 block font-medium">Customer</label><select name="customer_id" class="w-full rounded border-gray-300" required><option value="">Select a customer</option>@foreach ($customers as $customer)<option value="{{ $customer->id }}" @selected(old('customer_id') == $customer->id)>{{ $customer->name }}</option>@endforeach</select></div>
            @if ($products->isEmpty()) <p class="rounded bg-amber-100 p-3">No products are in stock. Add stock before creating an order.</p> @else
            <div><div class="mb-2 flex items-center justify-between"><h3 class="font-semibold">Items</h3><button type="button" id="add-item" class="rounded border px-3 py-1 text-sm">Add product</button></div>
                <div id="items" class="space-y-3"><div class="item-row flex gap-3"><select name="items[0][product_id]" class="flex-1 rounded border-gray-300" required><option value="">Select product</option>@foreach ($products as $product)<option value="{{ $product->id }}">{{ $product->name }} — ₱{{ number_format((float) $product->price, 2) }} (stock {{ $product->stock_quantity }})</option>@endforeach</select><input name="items[0][quantity]" type="number" min="1" max="{{ $products->max('stock_quantity') }}" value="1" class="w-24 rounded border-gray-300" required><button type="button" class="remove-item rounded border px-3 text-gray-600">Remove</button></div></div>
            </div>
            <p class="text-sm text-gray-500">Prices and totals are calculated from the current product prices when you save.</p>
            <button class="rounded bg-amber-800 px-5 py-2 text-white">Save order</button>
            @endif
        </form>
    </div></div></div>
    @if ($products->isNotEmpty())
    <script>
        let itemIndex = 1;
        const items = document.getElementById('items');
        document.getElementById('add-item').addEventListener('click', () => {
            const row = items.querySelector('.item-row').cloneNode(true);
            row.querySelectorAll('select, input').forEach((field) => {
                field.name = field.name.replace(/items\[\d+\]/, `items[${itemIndex}]`);
                if (field.tagName === 'SELECT') field.selectedIndex = 0;
                else field.value = 1;
            });
            items.appendChild(row); itemIndex++;
        });
        items.addEventListener('click', (event) => {
            if (event.target.classList.contains('remove-item') && items.querySelectorAll('.item-row').length > 1) event.target.closest('.item-row').remove();
        });
    </script>
    @endif
</x-app-layout>
