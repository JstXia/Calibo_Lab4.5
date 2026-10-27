# Café Counter

A small staff facing café order manager built with Laravel, Laravel Breeze (Blade), and MySQL.

## Setup

1. Create a MySQL database named `cafe_management` (or change `DB_DATABASE` in `.env`). Set `DB_USERNAME` and `DB_PASSWORD` for your local MySQL account.
2. Install dependencies and prepare the database:

   ```sh
   composer install
   npm install
   php artisan key:generate
   php artisan migrate --seed
   npm run build
   php artisan serve
   ```

3. Open `http://localhost:8000` and sign in with the seeded account:

   - Email: `staff@cafe.test`
   - Password: `password`

Laravel Breeze also provides registration, login, and logout screens. Registration is open by default; accounts can access the staff pages after signing in.

## Data model

The seven business tables are `users`, `categories`, `products`, `customers`, `orders`, `order_items`, and `payments`. Laravel's cache, queue, session, and password reset tables are infrastructure tables.

Order creation takes product prices from the database, stores each original price and computed line subtotal, calculates the order total from those subtotals, checks available stock, and writes the order in a transaction. Payment recording locks the order and rejects cumulative payments above its total. Foreign keys restrict deletion of products, customers, or staff with existing order references.
