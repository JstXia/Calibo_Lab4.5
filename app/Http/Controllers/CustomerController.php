<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomerController extends Controller
{
    public function index(): View { return view('customers.index', ['customers' => Customer::orderBy('name')->get()]); }

    public function store(Request $request): RedirectResponse
    {
        Customer::create($request->validate([
            'name' => ['required', 'string', 'max:150'], 'email' => ['nullable', 'email', 'max:255', 'unique:customers,email'],
            'phone' => ['nullable', 'string', 'max:20'], 'address' => ['nullable', 'string'],
        ]));
        return back()->with('status', 'Customer added.');
    }

    public function update(Request $request, Customer $customer): RedirectResponse
    {
        $customer->update($request->validate([
            'name' => ['required', 'string', 'max:150'], 'email' => ['nullable', 'email', 'max:255', 'unique:customers,email,'.$customer->id],
            'phone' => ['nullable', 'string', 'max:20'], 'address' => ['nullable', 'string'],
        ]));
        return back()->with('status', 'Customer updated.');
    }

    public function destroy(Customer $customer): RedirectResponse
    {
        try { $customer->delete(); }
        catch (QueryException) { return back()->withErrors(['customer' => 'This customer has existing orders and cannot be deleted.']); }
        return back()->with('status', 'Customer deleted.');
    }
}
