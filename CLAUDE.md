# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

CourierLab is a Laravel 11 courier/logistics management system with a multi-role architecture (Admin, Manager, Staff, Customer). The application runs in Docker with PHP 8.3, Apache, and MySQL 8.0. The main application resides in `Files/core/` with a custom entry point at `Files/index.php`.

## Docker Commands

All Laravel artisan commands must use the correct path inside containers:

```bash
# Container management
docker-compose start|stop|restart
docker-compose logs -f app
docker-compose exec app bash

# Laravel commands (note: Files/core/artisan path)
docker exec jattik_app php Files/core/artisan cache:clear
docker exec jattik_app php Files/core/artisan config:clear
docker exec jattik_app php Files/core/artisan route:list
docker exec jattik_app php Files/core/artisan migrate

# Database operations
docker exec jattik_mysql mysql -u root -proot courierlab
docker exec -i jattik_mysql mysql -u root -proot courierlab < backup.sql
docker exec jattik_mysql mysqldump -u root -proot courierlab > backup.sql

# Composer
docker exec jattik_app composer install --working-dir=Files/core
docker exec jattik_app composer update --working-dir=Files/core
```

**Important:** The artisan file is at `Files/core/artisan`, not `core/artisan`. The Docker document root is `/var/www/html/Files`.

## Architecture

### Multi-Role Authentication System

The application uses **4 separate user types** with distinct authentication flows:

1. **Admin** (`App\Models\Admin`)
   - Separate guard: `admin`
   - Routes: `routes/admin.php` (prefix: `/admin`)
   - Middleware: `RedirectIfNotAdmin`, `RedirectIfAdmin`
   - Full system access

2. **Manager** (`App\Models\User` with `user_type='manager'`)
   - Guard: `web`
   - Routes: `routes/manager.php` (prefix: `/manager`)
   - Middleware: `Manager`, `CheckStatus`
   - Manages branch operations and staff

3. **Staff** (`App\Models\User` with `user_type='staff'`)
   - Guard: `web`
   - Routes: `routes/staff.php` (prefix: `/staff`)
   - Middleware: `Staff`, `CheckStatus`
   - Handles courier operations and customers

4. **Customer** (`App\Models\Customer`)
   - No direct authentication
   - Managed by Staff through CRUD operations

### Key Controllers

Role-based controllers are namespaced:
- `App\Http\Controllers\Admin\*` - Admin operations
- `App\Http\Controllers\Manager\*` - Manager operations
- `App\Http\Controllers\Staff\*` - Staff operations
- `App\Http\Controllers\SiteController` - Public frontend
- `App\Http\Controllers\TicketController` - Support system

### Database Architecture

**Courier Workflow Tables:**
```
branches (physical locations)
    ↓
users (managers/staff assigned to branches)
    ↓
courier_infos (central courier entity)
    ├─→ sender_branch_id → branches
    ├─→ receiver_branch_id → branches
    ├─→ sender_staff_id → users
    ├─→ receiver_staff_id → users
    ├─→ sender_customer_id → customers
    └─→ receiver_customer_id → customers
    ↓
courier_products (items in shipment)
    └─→ type_id → types (pricing)
    └─→ unit_id → units
    ↓
courier_payments (payment tracking)
```

**Courier Status Flow:**
Defined in `app/Constants/Status.php`:
- `0` - COURIER_QUEUE (created, waiting)
- `1` - COURIER_DISPATCH/UPCOMING (in transit)
- `2` - COURIER_DELIVERYQUEUE (at destination branch)
- `3` - COURIER_DELIVERED (completed)

### Query Scopes & Branch Isolation

The `CourierInfo` model (`app/Models/CourierInfo.php`) has branch-aware scopes:

```php
CourierInfo::queue()        // sender's branch, status 0
CourierInfo::dispatch()     // sender's branch, status 1
CourierInfo::upcoming()     // receiver's branch, status 1
CourierInfo::deliveryQueue() // receiver's branch, status 2
CourierInfo::delivered()    // receiver's branch, status 3
```

These automatically filter by `auth()->user()->branch_id` to ensure staff only see relevant couriers.

### Searchable Pattern

All models use the `Searchable` trait registered as an Eloquent macro in `AppServiceProvider`:

```php
// app/Providers/AppServiceProvider.php
Builder::mixin(new Searchable);

// Usage in controllers
CourierInfo::dateFilter()
    ->searchable(['code', 'receiverBranch:name', 'senderCustomer:mobile'])
    ->filter(['status', 'receiver_branch_id'])
    ->paginate();
```

The `searchable()` method supports:
- Direct field search: `'code'`, `'name'`
- Relation search: `'receiverBranch:name'` (searches `branches.name`)
- Automatic LIKE queries with OR conditions

### Global Helpers

Auto-loaded via composer.json from `app/Http/Helpers/helpers.php`:

- `gs($key)` - Get cached general_settings value
- `notify($user, $template, $shortCodes)` - Send notifications
- `getTrx($length)` - Generate transaction codes
- `showAmount($amount)` - Format currency
- `activeTemplate()` - Get active frontend template name
- `getImage($path)` - Image URL with fallback
- `auth()->user()->branch_id` - Current user's branch (Manager/Staff)

### Notification System

Located in `app/Notify/`:
- Template-based (stored in `notification_templates` table)
- Multi-channel: Email (`app/Notify/Email.php`), SMS (`app/Notify/Sms.php`)
- Shortcode replacement mechanism
- Triggered via `notify($user, $template, $shortCodes)` helper

Gateways configured in Extensions (`extensions` table) and environment variables.

## Critical Files

| File | Purpose |
|------|---------|
| `Files/index.php` | Application entry point (bootstraps Laravel) |
| `Files/core/bootstrap/app.php` | Application bootstrap, route registration |
| `Files/core/config/auth.php` | Multi-guard authentication config |
| `Files/core/app/Constants/Status.php` | All status constants |
| `Files/core/app/Http/Helpers/helpers.php` | Auto-loaded global functions |
| `Files/core/app/Models/CourierInfo.php` | Core model with branch scopes |
| `Files/core/app/Lib/Searchable.php` | Search/filter trait for queries |
| `Files/core/app/Providers/AppServiceProvider.php` | Service registration, macros |
| `Files/install/database.sql` | Complete database schema (1030 lines) |

## Environment Configuration

Key `.env` variables for Docker:

```bash
APP_URL=http://localhost:8080
DB_HOST=mysql              # Docker service name
DB_PORT=3306
DB_DATABASE=courierlab
DB_USERNAME=root
DB_PASSWORD=root

SESSION_DRIVER=database    # requires 'sessions' table
CACHE_STORE=database       # requires 'cache' and 'cache_locks' tables
QUEUE_CONNECTION=database

MAIL_MAILER=log            # or smtp, sendgrid, etc.
```

**Important:** Cache and session tables (`cache`, `cache_locks`, `sessions`) are NOT in `database.sql`. Create manually if missing:

```bash
docker exec jattik_mysql mysql -u root -proot courierlab < Files/install/database.sql
```

Then create cache tables via artisan or manual SQL.

## Database Setup

The application uses a pre-built SQL file instead of migrations:

```bash
# Import full schema
docker exec -i jattik_mysql mysql -u root -proot courierlab < Files/install/database.sql

# Verify tables
docker exec jattik_mysql mysql -u root -proot -e "USE courierlab; SHOW TABLES;"
```

The SQL file includes:
- 26+ tables with schema and indexes
- Sample data for `general_settings`
- Initial admin user (check SQL file for credentials)

**Note:** `Files/core/database/migrations/` is empty. All schema is in `Files/install/database.sql`.

## Common Patterns

### Creating a Courier

See `app/Http/Controllers/Staff/CourierController.php@store`:

1. Validate/create sender customer (`customers` table)
2. Validate/create receiver customer
3. Create `courier_infos` record with:
   - `code` (unique tracking number via `getTrx()`)
   - `sender_branch_id` (staff's branch)
   - `receiver_branch_id` (selected branch)
   - `status = 0` (QUEUE)
4. Create `courier_products` records
5. Create `courier_payments` record with calculated amount
6. Send admin notification (`admin_notifications` table)

### Checking Permissions

```php
// Middleware checks
if (auth()->user()->user_type != 'manager') {
    return redirect()->route('manager.login');
}

// Branch ownership
$courier = CourierInfo::where('sender_branch_id', auth()->user()->branch_id)
    ->findOrFail($id);

// Status validation
if ($courier->status != Status::COURIER_QUEUE) {
    return back()->with('error', 'Cannot dispatch this courier');
}
```

### Adding Search/Filter

```php
// In controller
$results = Model::dateFilter()
    ->searchable(['field1', 'relation:field'])
    ->filter(['status', 'branch_id'])
    ->latest()
    ->paginate();

// Supports query params: ?search=term&status=1&date=2024-01-01
```

## Frontend Templates

Multiple template system in `resources/views/templates/`:
- `basic/` - Default template
- `sleek_craft/` - Alternative theme

Active template determined by `general_settings.active_template` (accessed via `activeTemplate()` helper).

Template assets use PHP for dynamic colors: `assets/templates/*/css/color.php`

## Payment Gateway Integration

Extensions stored in `extensions` table. Payment gateways available (via composer):
- AuthorizeNet (`authorizenet/authorizenet`)
- Mollie (`mollie/laravel-mollie`)
- BTCPay Server (`btcpayserver/btcpayserver-greenfield-php`)
- CoinGate (`coingate/coingate-php`)

Configure via Admin panel → Extensions section.

## Code Modification Guidelines

### When Adding Features

1. **Controllers:** Place in appropriate namespace (`Admin`, `Manager`, `Staff`)
2. **Routes:** Add to correct route file (`admin.php`, `manager.php`, `staff.php`)
3. **Middleware:** Apply role-based middleware (`admin`, `manager`, `staff`, `check.status`)
4. **Queries:** Use scopes for branch isolation (e.g., `where('branch_id', auth()->user()->branch_id)`)
5. **Search:** Use `searchable()` method for consistent filtering
6. **Notifications:** Use `notify()` helper with template shortcodes

### When Modifying Database

- Update `Files/install/database.sql` (no migrations system)
- Run manual SQL commands in Docker container
- Ensure cache/session tables exist if using database drivers

### Testing Changes

```bash
# Clear all caches after config/route changes
docker exec jattik_app php Files/core/artisan config:clear
docker exec jattik_app php Files/core/artisan route:clear
docker exec jattik_app php Files/core/artisan view:clear
docker exec jattik_app php Files/core/artisan cache:clear

# Restart containers if .env modified
docker-compose restart

# Check logs
docker-compose logs -f app
```

## Access URLs

- Application: http://localhost:8080
- phpMyAdmin: http://localhost:8081
- Admin panel: http://localhost:8080/admin
- Manager panel: http://localhost:8080/manager
- Staff panel: http://localhost:8080/staff

## Installation/Activation System

Located in `Files/install/`:
- First-time access redirects to `/activate`
- License validation system (ViserLab)
- After activation, application becomes accessible

Database already imported during Docker setup, so activation may show as complete.
