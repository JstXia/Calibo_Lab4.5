<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(): View { return view('products.index', ['products' => Product::with('category')->orderBy('name')->get(), 'categories' => Category::orderBy('name')->get()]); }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'category_id' => ['required', 'exists:categories,id'], 'name' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string'], 'price' => ['required', 'numeric', 'gt:0', 'decimal:0,2'],
            'stock_quantity' => ['required', 'integer', 'min:0'], 'is_available' => ['sometimes', 'boolean'],
        ]);
        $data['is_available'] = $request->boolean('is_available');
        Product::create($data);
        return back()->with('status', 'Product added.');
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $product->update($request->validate([
            'category_id' => ['required', 'exists:categories,id'], 'name' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string'], 'price' => ['required', 'numeric', 'gt:0', 'decimal:0,2'],
            'stock_quantity' => ['required', 'integer', 'min:0'], 'is_available' => ['sometimes', 'boolean'],
        ]) + ['is_available' => $request->boolean('is_available')]);
        return back()->with('status', 'Product updated.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        try { $product->delete(); }
        catch (QueryException) { return back()->withErrors(['product' => 'This product is used in an existing order and cannot be deleted.']); }
        return back()->with('status', 'Product deleted.');
    }
}
