<x-app-layout>
    <x-slot name="header"><h2 class="text-xl font-semibold text-gray-800">Customers</h2></x-slot>
    <div class="py-8"><div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
        @if (session('status')) <div class="rounded bg-green-100 p-3 text-green-800">{{ session('status') }}</div> @endif
        @if ($errors->any()) <div class="rounded bg-red-100 p-3 text-red-800">{{ $errors->first() }}</div> @endif
        <form method="POST" action="{{ route('customers.store') }}" class="grid gap-3 rounded-lg bg-white p-5 shadow sm:grid-cols-2 lg:grid-cols-5">@csrf
            <input name="name" placeholder="Customer name" class="rounded border-gray-300" required><input name="email" type="email" placeholder="Email" class="rounded border-gray-300"><input name="phone" placeholder="Phone" class="rounded border-gray-300"><input name="address" placeholder="Address" class="rounded border-gray-300"><button class="rounded bg-amber-800 px-4 py-2 text-white">Add customer</button>
        </form>
        <div class="overflow-x-auto rounded-lg bg-white shadow"><table class="min-w-full divide-y text-left"><thead class="bg-gray-50 text-xs uppercase"><tr><th class="px-5 py-3">Name</th><th class="px-5 py-3">Email</th><th class="px-5 py-3">Phone</th><th class="px-5 py-3">Address</th><th></th></tr></thead><tbody class="divide-y">
            @foreach ($customers as $customer)<tr><td class="px-5 py-4">{{ $customer->name }}</td><td class="px-5 py-4">{{ $customer->email ?? '—' }}</td><td class="px-5 py-4">{{ $customer->phone ?? '—' }}</td><td class="px-5 py-4">{{ $customer->address ?? '—' }}</td><td class="space-y-2 px-5 py-4"><details><summary class="cursor-pointer text-amber-900">Edit</summary><form method="POST" action="{{ route('customers.update', $customer) }}" class="mt-2 grid gap-2">@csrf @method('PATCH')<input name="name" value="{{ $customer->name }}" class="rounded border-gray-300" required><input name="email" type="email" value="{{ $customer->email }}" class="rounded border-gray-300"><input name="phone" value="{{ $customer->phone }}" class="rounded border-gray-300"><textarea name="address" class="rounded border-gray-300">{{ $customer->address }}</textarea><button class="rounded bg-gray-800 px-3 py-2 text-white">Save changes</button></form></details><form method="POST" action="{{ route('customers.destroy', $customer) }}" onsubmit="return confirm('Delete this customer?')">@csrf @method('DELETE')<button class="text-red-700">Delete</button></form></td></tr>@endforeach
        </tbody></table></div>
    </div></div>
</x-app-layout>
