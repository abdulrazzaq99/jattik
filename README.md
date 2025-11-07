# CourierLab - Courier Management System

A comprehensive Laravel 11 courier/logistics management system with multi-role architecture, real-time tracking, subscription management, and advanced analytics.

## Table of Contents

- [Features](#features)
- [System Requirements](#system-requirements)
- [Installation Guide](#installation-guide)
- [Docker Setup](#docker-setup)
- [Database Setup](#database-setup)
- [Configuration](#configuration)
- [Running the Application](#running-the-application)
- [Default Credentials](#default-credentials)
- [Common Issues](#common-issues)
- [Useful Commands](#useful-commands)

---

## Features

### Core Features
- **Multi-Role Authentication System**
  - Admin (full system access)
  - Manager (branch operations)
  - Staff (courier operations)
  - Customer (self-service portal)

### Advanced Features (FR-14 to FR-36)
- Shipment Management (address books, quotes, scheduling, warehouse holdings)
- Subscription Management (monthly plans, coupons, payment processing)
- Insurance Management (policies, claims with 10-day SLA)
- Real-time Tracking (courier API integration, exception handling)
- Notifications (facility arrival, dispatch, exceptions, fee quotes)
- Customer Support (issue reporting, claims processing, contact forms)
- WhatsApp Chatbot (AI-powered support, escalation management)
- Ratings & Feedback System
- Analytics Dashboard (shipping costs, carrier performance, regional analytics)

---

## System Requirements

- **Docker Desktop** (Windows/Mac/Linux)
- **Git** (for version control)
- **Minimum 4GB RAM** (8GB recommended)
- **5GB Free Disk Space**

### Pre-installed in Docker Containers
- PHP 8.3
- Apache 2.4
- MySQL 8.0
- Composer
- Node.js (optional for asset compilation)

---

## Installation Guide

### Step 1: Clone the Repository

```bash
# Clone the repository
git clone <your-repository-url>
cd jattik

# Or if you have the files already, navigate to the project directory
cd C:\Users\hp\Desktop\jattik
```

### Step 2: Verify Project Structure

Ensure these key directories exist:
```
jattik/
├── Files/
│   ├── core/                 # Laravel application
│   │   ├── app/
│   │   ├── config/
│   │   ├── database/
│   │   ├── resources/
│   │   ├── routes/
│   │   └── artisan
│   ├── index.php             # Application entry point
│   └── install/
│       └── database.sql      # Database schema
├── docker-compose.yml
└── README.md
```

---

## Docker Setup

### Step 1: Start Docker Desktop

Ensure Docker Desktop is running on your machine.

### Step 2: Build and Start Containers

```bash
# Build and start all containers
docker-compose up -d

# Check if containers are running
docker-compose ps
```

You should see 3 containers running:
- `jattik_app` (PHP/Apache)
- `jattik_mysql` (MySQL)
- `jattik_phpmyadmin` (phpMyAdmin)

### Step 3: Verify Container Status

```bash
# Check logs if any container failed
docker-compose logs -f app
docker-compose logs -f mysql

# Access the app container shell
docker exec -it jattik_app bash
```

---

## Database Setup

### Option 1: Import Complete Database (Recommended)

```bash
# Import the complete database schema with sample data
docker exec -i jattik_mysql mysql -u root -proot courierlab < Files/install/database.sql

# Verify tables were created
docker exec jattik_mysql mysql -u root -proot -e "USE courierlab; SHOW TABLES;"
```

### Option 2: Run Migrations (If database.sql doesn't work)

```bash
# Run all migrations
docker exec jattik_app php Files/core/artisan migrate

# Run specific migration batches
docker exec jattik_app php Files/core/artisan migrate --path=database/migrations/2025_11_06_100001_create_subscription_plans_table.php
docker exec jattik_app php Files/core/artisan migrate --path=database/migrations/2025_11_06_100002_create_customer_subscriptions_table.php
docker exec jattik_app php Files/core/artisan migrate --path=database/migrations/2025_11_06_100003_create_payments_table.php
docker exec jattik_app php Files/core/artisan migrate --path=database/migrations/2025_11_06_100004_create_insurance_policies_table.php
docker exec jattik_app php Files/core/artisan migrate --path=database/migrations/2025_11_07_100001_create_customer_addresses_table.php
docker exec jattik_app php Files/core/artisan migrate --path=database/migrations/2025_11_07_100002_create_shipment_schedules_table.php
docker exec jattik_app php Files/core/artisan migrate --path=database/migrations/2025_11_07_100003_create_warehouse_holdings_table.php
docker exec jattik_app php Files/core/artisan migrate --path=database/migrations/2025_11_07_120001_create_shipment_notifications_table.php
docker exec jattik_app php Files/core/artisan migrate --path=database/migrations/2025_11_07_120002_create_tracking_events_table.php
docker exec jattik_app php Files/core/artisan migrate --path=database/migrations/2025_11_07_120003_create_support_issues_table.php
docker exec jattik_app php Files/core/artisan migrate --path=database/migrations/2025_11_07_120004_create_claims_table.php
docker exec jattik_app php Files/core/artisan migrate --path=database/migrations/2025_11_07_120005_create_shipment_ratings_table.php
docker exec jattik_app php Files/core/artisan migrate --path=database/migrations/2025_11_07_120006_create_whatsapp_messages_table.php
docker exec jattik_app php Files/core/artisan migrate --path=database/migrations/2025_11_07_120007_create_contact_messages_table.php
```

### Step 3: Seed Sample Data (Optional)

```bash
# Seed subscription plans
docker exec jattik_app php Files/core/artisan db:seed --class=SubscriptionPlansSeeder

# Seed coupons
docker exec jattik_app php Files/core/artisan db:seed --class=CouponsSeeder

# Note: Other seeders might be available. Check database/seeders/
```

---

## Configuration

### Step 1: Environment File

The `.env` file should already exist in `Files/core/.env`. Verify these key settings:

```bash
# View environment file
cat Files/core/.env

# Key settings to verify:
APP_URL=http://localhost:8080
DB_HOST=mysql              # Docker service name
DB_PORT=3306
DB_DATABASE=courierlab
DB_USERNAME=root
DB_PASSWORD=root
```

### Step 2: Generate Application Key (If needed)

```bash
# Generate Laravel application key
docker exec jattik_app php Files/core/artisan key:generate
```

### Step 3: Clear All Caches

```bash
# Clear all caches
docker exec jattik_app php Files/core/artisan cache:clear
docker exec jattik_app php Files/core/artisan config:clear
docker exec jattik_app php Files/core/artisan route:clear
docker exec jattik_app php Files/core/artisan view:clear
```

### Step 4: Set File Permissions (Linux/Mac only)

```bash
# If running on Linux/Mac, set proper permissions
docker exec jattik_app chmod -R 775 Files/core/storage
docker exec jattik_app chmod -R 775 Files/core/bootstrap/cache
```

---

## Running the Application

### Step 1: Access the Application

Open your browser and navigate to:

- **Main Application**: http://localhost:8080
- **phpMyAdmin**: http://localhost:8081
  - Username: `root`
  - Password: `root`

### Step 2: Access Different Portals

- **Admin Panel**: http://localhost:8080/admin
- **Manager Panel**: http://localhost:8080/manager
- **Staff Panel**: http://localhost:8080/staff
- **Customer Portal**: http://localhost:8000/customer/register

---

## Default Credentials

### Admin Login
Check the `Files/install/database.sql` file for default admin credentials, or create one:

```bash
# Access MySQL container
docker exec -it jattik_mysql mysql -u root -proot courierlab

# Create admin user
INSERT INTO admins (name, email, username, password, created_at, updated_at)
VALUES ('Super Admin', 'admin@admin.com', 'admin', '$2y$10$kMXvhZ6QzZ5nZYz5nZYz5.', NOW(), NOW());
```

**Default credentials (check database.sql for exact values):**
- Username: `admin`
- Password: `password` (or as specified in SQL file)

### Manager/Staff Login
These are created through the Admin panel after logging in.

---

## Common Issues

### Issue 1: Containers Won't Start

```bash
# Stop all containers
docker-compose down

# Remove volumes (WARNING: This deletes data)
docker-compose down -v

# Rebuild containers
docker-compose up -d --build

# Check logs
docker-compose logs -f
```

### Issue 2: Database Connection Failed

```bash
# Verify MySQL is running
docker-compose ps

# Check MySQL logs
docker-compose logs mysql

# Restart MySQL container
docker-compose restart mysql

# Wait 30 seconds, then test connection
docker exec jattik_mysql mysql -u root -proot -e "SELECT 1;"
```

### Issue 3: Port Already in Use

If ports 8080, 3306, or 8081 are already in use, edit `docker-compose.yml`:

```yaml
services:
  app:
    ports:
      - "8888:80"  # Change 8080 to 8888
  mysql:
    ports:
      - "3307:3306"  # Change 3306 to 3307
  phpmyadmin:
    ports:
      - "8082:80"  # Change 8081 to 8082
```

Then restart containers:
```bash
docker-compose down
docker-compose up -d
```

### Issue 4: View Errors (Undefined Variables)

```bash
# Clear view cache
docker exec jattik_app php Files/core/artisan view:clear

# Clear all caches
docker exec jattik_app php Files/core/artisan optimize:clear
```

### Issue 5: Migration Errors

```bash
# Rollback all migrations
docker exec jattik_app php Files/core/artisan migrate:rollback

# Or reset database
docker exec jattik_app php Files/core/artisan migrate:fresh

# Re-import database.sql
docker exec -i jattik_mysql mysql -u root -proot courierlab < Files/install/database.sql
```

---

## Useful Commands

### Docker Commands

```bash
# Start containers
docker-compose start

# Stop containers (keeps data)
docker-compose stop

# Restart containers
docker-compose restart

# View logs
docker-compose logs -f app
docker-compose logs -f mysql

# Access app container
docker exec -it jattik_app bash

# Access MySQL container
docker exec -it jattik_mysql mysql -u root -proot courierlab

# Remove all containers and volumes (DANGER: Deletes data)
docker-compose down -v
```

### Laravel Artisan Commands

```bash
# View all routes
docker exec jattik_app php Files/core/artisan route:list

# Create database backup
docker exec jattik_mysql mysqldump -u root -proot courierlab > backup_$(date +%Y%m%d).sql

# Restore database backup
docker exec -i jattik_mysql mysql -u root -proot courierlab < backup_20250107.sql

# Clear caches
docker exec jattik_app php Files/core/artisan cache:clear
docker exec jattik_app php Files/core/artisan config:clear
docker exec jattik_app php Files/core/artisan route:clear
docker exec jattik_app php Files/core/artisan view:clear

# Run migrations
docker exec jattik_app php Files/core/artisan migrate

# Rollback last migration
docker exec jattik_app php Files/core/artisan migrate:rollback

# Run seeders
docker exec jattik_app php Files/core/artisan db:seed
docker exec jattik_app php Files/core/artisan db:seed --class=SubscriptionPlansSeeder
```

### Composer Commands

```bash
# Install dependencies
docker exec jattik_app composer install --working-dir=Files/core

# Update dependencies
docker exec jattik_app composer update --working-dir=Files/core

# Dump autoload
docker exec jattik_app composer dump-autoload --working-dir=Files/core
```

---

## Project Structure

```
Files/core/
├── app/
│   ├── Constants/
│   │   └── Status.php                    # Status constants
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/                    # Admin controllers
│   │   │   ├── Customer/                 # Customer controllers
│   │   │   ├── Manager/                  # Manager controllers
│   │   │   └── Staff/                    # Staff controllers
│   │   ├── Helpers/
│   │   │   └── helpers.php               # Global helper functions
│   │   └── Middleware/                   # Authentication middleware
│   ├── Models/                           # Eloquent models
│   ├── Notify/                           # Notification system
│   └── Services/                         # Business logic services
├── config/                               # Configuration files
├── database/
│   ├── migrations/                       # Database migrations
│   └── seeders/                          # Database seeders
├── resources/
│   └── views/
│       ├── admin/                        # Admin views
│       ├── customer/                     # Customer views
│       ├── manager/                      # Manager views
│       └── staff/                        # Staff views
├── routes/
│   ├── admin.php                         # Admin routes
│   ├── customer.php                      # Customer routes
│   ├── manager.php                       # Manager routes
│   ├── staff.php                         # Staff routes
│   └── web.php                           # Public routes
└── storage/                              # File storage
```

---

## Next Steps

After successful installation:

1. **Login to Admin Panel**: http://localhost:8080/admin
2. **Create Branches**: Add branch locations
3. **Create Managers**: Assign managers to branches
4. **Create Staff**: Add staff members to branches
5. **Configure Settings**: Set up general settings, notification templates
6. **Test Features**: Follow the COMPLETE_WALKTHROUGH.md guide

---

## Support & Documentation

- **Complete Walkthrough**: See `COMPLETE_WALKTHROUGH.md` for detailed feature testing
- **API Documentation**: See `FRONTEND_VIEWS_COMPLETE.md` for view structure
- **Laravel Documentation**: https://laravel.com/docs/11.x

---

## License

Proprietary - CourierLab by ViserLab

---

## Version

- **Laravel**: 11.0
- **PHP**: 8.3
- **MySQL**: 8.0
- **Last Updated**: 2025-11-07
