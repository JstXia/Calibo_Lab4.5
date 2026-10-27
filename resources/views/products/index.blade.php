<x-app-layout>
    <x-slot name="header"><h2 class="text-xl font-semibold text-gray-800">Products</h2></x-slot>
    <div class="py-8"><div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
        @if (session('status')) <div class="rounded bg-green-100 p-3 text-green-800">{{ session('status') }}</div> @endif
        @if ($errors->any()) <div class="rounded bg-red-100 p-3 text-red-800">{{ $errors->first() }}</div> @endif
        <form method="POST" action="{{ route('products.store') }}" class="grid gap-3 rounded-lg bg-white p-5 shadow sm:grid-cols-2 lg:grid-cols-6">@csrf
            <input name="name" placeholder="Product name" value="{{ old('name') }}" class="rounded border-gray-300" required>
            <select name="category_id" class="rounded border-gray-300" required><option value="">Category</option>@foreach ($categories as $category)<option value="{{ $category->id }}">{{ $category->name }}</option>@endforeach</select>
            <input name="price" type="number" min="0.01" step="0.01" placeholder="Price" class="rounded border-gray-300" required>
            <input name="stock_quantity" type="number" min="0" placeholder="Stock" class="rounded border-gray-300" required>
            <input name="description" placeholder="Description (optional)" class="rounded border-gray-300">
            <button class="rounded bg-amber-800 px-4 py-2 text-white">Add product</button>
            <label class="text-sm"><input name="is_available" type="checkbox" value="1" checked> Available for sale</label>
        </form>
        <div class="overflow-x-auto rounded-lg bg-white shadow"><table class="min-w-full divide-y text-left"><thead class="bg-gray-50 text-xs uppercase"><tr><th class="px-5 py-3">Product</th><th class="px-5 py-3">Category</th><th class="px-5 py-3">Price</th><th class="px-5 py-3">Stock</th><th class="px-5 py-3">Available</th><th></th></tr></thead><tbody class="divide-y">
            @foreach ($products as $product)<tr><td class="px-5 py-4">{{ $product->name }}</td><td class="px-5 py-4">{{ $product->category->name }}</td><td class="px-5 py-4">₱{{ number_format((float) $product->price, 2) }}</td><td class="px-5 py-4">{{ $product->stock_quantity }}</td><td class="px-5 py-4">{{ $product->is_available ? 'Yes' : 'No' }}</td><td class="space-y-2 px-5 py-4"><details><summary class="cursor-pointer text-amber-900">Edit</summary><form method="POST" action="{{ route('products.update', $product) }}" class="mt-2 grid gap-2">@csrf @method('PATCH')<input name="name" value="{{ $product->name }}" class="rounded border-gray-300" required><select name="category_id" class="rounded border-gray-300">@foreach ($categories as $category)<option value="{{ $category->id }}" @selected($product->category_id === $category->id)>{{ $category->name }}</option>@endforeach</select><input name="price" type="number" min="0.01" step="0.01" value="{{ $product->price }}" class="rounded border-gray-300" required><input name="stock_quantity" type="number" min="0" value="{{ $product->stock_quantity }}" class="rounded border-gray-300" required><textarea name="description" class="rounded border-gray-300">{{ $product->description }}</textarea><label><input name="is_available" type="checkbox" value="1" @checked($product->is_available)> Available</label><button class="rounded bg-gray-800 px-3 py-2 text-white">Save changes</button></form></details><form method="POST" action="{{ route('products.destroy', $product) }}" onsubmit="return confirm('Delete this product?')">@csrf @method('DELETE')<button class="text-red-700">Delete</button></form></td></tr>@endforeach
        </tbody></table></div>
    </div></div>
</x-app-layout>
