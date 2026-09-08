# Cafe Management System — setup

Laravel 12 + Blade + Bootstrap 5 (CDN) + MySQL. No npm build step is required.

## 1. Edit `.env` yourself

The `.env` file cannot be written remotely, so open
`D:\laravel\cafe-management\.env` and make these two changes.

Replace the database block:

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=cafe_management
DB_USERNAME=root
DB_PASSWORD=
```

And append at the end of the file:

```
APP_NAME="Cafe Management"

CAFE_NAME="My Cafe"
CAFE_ADDRESS="Kathmandu, Nepal"
CAFE_PHONE="+977-9800000000"
CAFE_CURRENCY="Rs."
CAFE_SERVICE_CHARGE=10
CAFE_TAX_RATE=13
```

(`APP_NAME` already exists near the top — change it there instead of adding a
second one.)

## 2. Create the database

In phpMyAdmin / MySQL:

```sql
CREATE DATABASE cafe_management CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

If your MySQL user is not `root` with a blank password, adjust `DB_USERNAME` / `DB_PASSWORD` in `.env`.

## 3. Migrate and seed

```bash
php artisan migrate:fresh --seed
php artisan storage:link      # so menu item photos display
php artisan optimize:clear
```

`migrate:fresh` drops existing tables — this project was still a blank skeleton, so that is safe here.

## 4. Run it

```bash
php artisan serve
```

Open http://127.0.0.1:8000

## Demo logins

| Role    | Email              | Password |
|---------|--------------------|----------|
| Admin   | admin@cafe.test    | password |
| Cashier | cashier@cafe.test  | password |

Change these before real use (Staff → Edit).

## Cafe settings

`CAFE_SERVICE_CHARGE` and `CAFE_TAX_RATE` are percentages — set either to `0` to
switch it off. Defaults live in `config/cafe.php`. Run `php artisan config:clear`
after changing any of them.

## Payment QR code

When settling a bill as **Online**, the order page can show a "scan to pay" QR
code with the bill amount, your bank name, account name and account number as
text. It only appears once you fill in `CAFE_BANK_NAME`, `CAFE_BANK_ACCOUNT_NAME`
and `CAFE_BANK_ACCOUNT_NUMBER` in `.env` — otherwise a reminder to fill them in
is shown instead. This is an informational QR: the customer's banking/wallet
app shows them your account details and they confirm the transfer themselves,
it does not auto-fill the amount the way a registered merchant QR (eSewa,
Khalti, Fonepay) would.

## How the pieces fit together

- **POS** — tap menu items to build a bill, pick a table for dine-in, save it open or
  go straight to payment. Saving an order marks its table occupied.
- **Orders & bills** — every order, open or settled. Settling a bill records the
  payment method, calculates change, frees the table and prints a receipt.
- **Menu** — categories and items. Each item can have a **recipe**: the ingredients
  it consumes per unit sold.
- **Inventory** — ingredients with a reorder level. Stock only moves through
  recorded movements (purchase, wastage, adjustment), plus automatic **usage**
  deducted from recipes when a bill is paid, so the history always explains the balance.
- **Tables & reservations** — floor status board and bookings; a booked table shows
  as reserved and frees up when the reservation is completed or cancelled.
- **Admin only** — sales report (by day, payment method and item) and staff accounts.
  Cashiers cannot reach either.

## Roles

`users.role` is `admin` or `cashier`. The `admin` middleware alias (registered in
`bootstrap/app.php`) guards the admin-only routes.
