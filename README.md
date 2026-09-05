# Cafe Billing Management System

A Laravel 12 + Blade + Bootstrap 5 point-of-sale and billing system for a cafe —
built around fast order entry, printed receipts, table/reservation tracking,
recipe-based stock deduction, and sales reporting.

## Features

- **Dashboard** — today's sales, bill count, open orders, low-stock alerts,
  upcoming reservations, a 7-day sales chart, and best sellers.
- **POS / billing** — tap menu items to build a bill, search and filter by
  category, adjust quantity, apply a discount, auto-calculate service charge
  and tax, and save the order open or take payment immediately.
- **Orders & receipts** — every order (open or settled) with its full item
  breakdown. Print the bill before payment or the final receipt after —
  formatted for narrow thermal-printer paper.
- **Menu management** — categories and items, with price, availability, and
  an optional photo per item.
- **Recipes & inventory** — attach ingredients to a menu item so stock is
  deducted automatically when a bill is paid; ingredients carry a reorder
  level and a full purchase/wastage/adjustment history.
- **Tables & reservations** — a floor status board (available / occupied /
  reserved) and a reservation list; seating or completing a booking updates
  the table automatically.
- **Customers** — track name, phone, and email per customer, with automatic
  order-history and total-spend rollups tied to their bills.
- **Sales reports** — date-range filtering broken down by day, by payment
  method, and by item (admin only).
- **Staff & roles** — Admin and Cashier accounts with role-gated access to
  staff management and sales reports.

## Tech stack

Laravel 12, Blade templates, Bootstrap 5 + Bootstrap Icons (via CDN, no
frontend build step required), MySQL.

## Getting started

See [SETUP.md](SETUP.md) for the full setup guide, including `.env`
configuration, database creation, migrations, seeding, and demo login
credentials.

## License

The Laravel framework this project is built on is open-sourced software
licensed under the [MIT license](https://opensource.org/licenses/MIT).
