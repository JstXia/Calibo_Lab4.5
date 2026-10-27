<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $staff = User::updateOrCreate(['email' => 'staff@cafe.test'], [
            'name' => 'Café Staff', 'password' => 'password', 'email_verified_at' => now(),
        ]);

        $categories = collect([
            ['name' => 'Coffee', 'description' => 'Espresso and brewed coffee'],
            ['name' => 'Tea', 'description' => 'Hot and iced tea'],
            ['name' => 'Bakery', 'description' => 'Fresh café pastries'],
        ])->mapWithKeys(fn ($data) => [$data['name'] => Category::updateOrCreate(['name' => $data['name']], $data)]);

        $products = collect([
            ['name' => 'Americano', 'category' => 'Coffee', 'price' => '120.00', 'stock_quantity' => 40],
            ['name' => 'Cappuccino', 'category' => 'Coffee', 'price' => '150.00', 'stock_quantity' => 35],
            ['name' => 'Vanilla Latte', 'category' => 'Coffee', 'price' => '165.00', 'stock_quantity' => 30],
            ['name' => 'Matcha Latte', 'category' => 'Tea', 'price' => '170.00', 'stock_quantity' => 25],
            ['name' => 'Iced Lemon Tea', 'category' => 'Tea', 'price' => '110.00', 'stock_quantity' => 30],
            ['name' => 'Butter Croissant', 'category' => 'Bakery', 'price' => '95.00', 'stock_quantity' => 24],
        ])->mapWithKeys(function ($data) use ($categories) {
            $category = $data['category'];
            unset($data['category']);
            return [$data['name'] => Product::updateOrCreate(['name' => $data['name']], $data + ['category_id' => $categories[$category]->id, 'description' => null, 'is_available' => true])];
        });

        $customers = collect([
            ['name' => 'Alex Santos', 'email' => 'alex@example.test', 'phone' => '09171234567'],
            ['name' => 'Jamie Cruz', 'email' => 'jamie@example.test', 'phone' => '09181234567'],
            ['name' => 'Morgan Reyes', 'email' => 'morgan@example.test', 'phone' => '09191234567'],
        ])->map(fn ($data) => Customer::updateOrCreate(['email' => $data['email']], $data + ['address' => null]))->values();

        $orderLines = [
            [['Americano', 2], ['Butter Croissant', 1]],
            [['Cappuccino', 1], ['Vanilla Latte', 1]],
            [['Matcha Latte', 1], ['Iced Lemon Tea', 2]],
        ];
        foreach ($orderLines as $index => $lines) {
            $order = Order::updateOrCreate(['order_number' => 'DEMO-'.str_pad((string) ($index + 1), 4, '0', STR_PAD_LEFT)], [
                'customer_id' => $customers[$index]->id, 'user_id' => $staff->id,
                'order_date' => now()->subDays(2 - $index), 'status' => $index < 2 ? 'paid' : 'pending', 'total_amount' => '0.00',
            ]);
            $order->items()->delete();
            $totalCents = 0;
            foreach ($lines as [$name, $quantity]) {
                $product = $products[$name];
                $unitCents = (int) round((float) $product->price * 100);
                $subtotalCents = $unitCents * $quantity;
                $totalCents += $subtotalCents;
                $order->items()->create([
                    'product_id' => $product->id, 'quantity' => $quantity,
                    'unit_price' => number_format($unitCents / 100, 2, '.', ''),
                    'subtotal' => number_format($subtotalCents / 100, 2, '.', ''),
                ]);
            }
            $order->update(['total_amount' => number_format($totalCents / 100, 2, '.', '')]);

            if ($index < 2) {
                Payment::updateOrCreate(['payment_reference' => 'DEMO-PAY-'.($index + 1)], [
                    'order_id' => $order->id, 'payment_method' => $index === 0 ? 'cash' : 'card',
                    'amount' => number_format($totalCents / 100, 2, '.', ''), 'paid_at' => now()->subDays(2 - $index),
                ]);
            }
        }
    }
}
