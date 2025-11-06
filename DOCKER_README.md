# Docker Setup Guide for Jattik Courier

This guide will help you run the Jattik Courier Laravel application using Docker, avoiding conflicts with your existing XAMPP installation.

## Prerequisites

- Docker Desktop installed and running on your system
- Git Bash (for running .sh scripts on Windows) - optional

## Quick Start

### Option 1: Automated Setup (Recommended)

**On Windows:**
```bash
setup.bat
```

**On Linux/Mac or Git Bash:**
```bash
chmod +x setup.sh
./setup.sh
```

The script will:
1. Build Docker containers
2. Start all services (PHP, MySQL, phpMyAdmin)
3. Generate Laravel application key
4. Run database migrations

### Option 2: Manual Setup

1. **Build and start containers:**
   ```bash
   docker-compose up --build -d
   ```

2. **Wait for MySQL to be ready (about 10 seconds)**

3. **Generate Laravel application key:**
   ```bash
   docker-compose exec app php core/artisan key:generate
   ```

4. **Run database migrations:**
   ```bash
   docker-compose exec app php core/artisan migrate
   ```

## Access Points

- **Application:** http://localhost:8080
- **phpMyAdmin:** http://localhost:8081 (for database management)

## Database Credentials

- **Host:** `mysql` (from within the app container) or `localhost:3306` (from your host machine)
- **Database:** `courierlab`
- **Username:** `root`
- **Password:** `root`

Alternative user:
- **Username:** `jattik`
- **Password:** `jattik`

## Common Commands

### Start/Stop Containers

```bash
# Stop containers (data is preserved)
docker-compose stop

# Start stopped containers
docker-compose start

# Restart containers
docker-compose restart

# Stop and remove containers (data in volumes is preserved)
docker-compose down

# Stop and remove containers + volumes (DELETES DATABASE)
docker-compose down -v
```

### View Logs

```bash
# View all logs
docker-compose logs

# Follow logs in real-time
docker-compose logs -f

# View app logs only
docker-compose logs -f app

# View MySQL logs only
docker-compose logs -f mysql
```

### Execute Commands

```bash
# Open bash shell in app container
docker-compose exec app bash

# Run artisan commands
docker-compose exec app php core/artisan <command>

# Examples:
docker-compose exec app php core/artisan migrate
docker-compose exec app php core/artisan db:seed
docker-compose exec app php core/artisan cache:clear
docker-compose exec app php core/artisan config:clear
docker-compose exec app php core/artisan route:list

# Run composer commands
docker-compose exec app composer install
docker-compose exec app composer update
```

### Database Operations

```bash
# Access MySQL CLI
docker-compose exec mysql mysql -u root -proot courierlab

# Import SQL file
docker-compose exec -T mysql mysql -u root -proot courierlab < backup.sql

# Export database
docker-compose exec mysql mysqldump -u root -proot courierlab > backup.sql
```

## Project Structure

```
jattik/
├── Dockerfile              # PHP 8.3 + Apache configuration
├── docker-compose.yml      # Multi-container setup
├── .dockerignore          # Files to exclude from Docker build
├── setup.bat              # Windows setup script
├── setup.sh               # Linux/Mac setup script
├── Files/                 # Laravel application root
│   ├── index.php         # Application entry point
│   ├── .htaccess         # Apache rewrite rules
│   └── core/             # Laravel core files
│       ├── app/
│       ├── config/
│       ├── database/
│       ├── .env          # Environment configuration
│       └── ...
└── Documents/            # Documentation files
```

## Services Included

1. **app** - PHP 8.3 with Apache
   - Includes: mysqli, pdo_mysql, gd, zip, mbstring, exif, pcntl, bcmath
   - Document root: `/var/www/html/Files`
   - Port: 8080 (host) → 80 (container)

2. **mysql** - MySQL 8.0
   - Port: 3306 (host) → 3306 (container)
   - Persistent volume: `mysql-data`

3. **phpmyadmin** - Database management tool
   - Port: 8081 (host) → 80 (container)

## Troubleshooting

### Port Conflicts

If ports 8080, 3306, or 8081 are already in use, edit `docker-compose.yml`:

```yaml
services:
  app:
    ports:
      - "9000:80"  # Change 8080 to 9000
  mysql:
    ports:
      - "3307:3306"  # Change 3306 to 3307
  phpmyadmin:
    ports:
      - "9001:80"  # Change 8081 to 9001
```

### Permission Issues

If you encounter permission errors:

```bash
# Fix storage permissions
docker-compose exec app chown -R www-data:www-data Files/core/storage
docker-compose exec app chmod -R 775 Files/core/storage
```

### Database Connection Issues

1. Make sure MySQL is fully started:
   ```bash
   docker-compose logs mysql
   ```

2. Check if MySQL is accepting connections:
   ```bash
   docker-compose exec mysql mysql -u root -proot -e "SELECT 1"
   ```

3. Verify `.env` file has correct settings:
   ```
   DB_HOST=mysql
   DB_PORT=3306
   DB_DATABASE=courierlab
   DB_USERNAME=root
   DB_PASSWORD=root
   ```

### Container Won't Start

```bash
# View detailed error logs
docker-compose logs app

# Rebuild containers from scratch
docker-compose down
docker-compose build --no-cache
docker-compose up -d
```

### Clear Laravel Cache

```bash
docker-compose exec app php core/artisan cache:clear
docker-compose exec app php core/artisan config:clear
docker-compose exec app php core/artisan route:clear
docker-compose exec app php core/artisan view:clear
```

## Development Workflow

Your code changes in the `Files/` directory are automatically reflected in the container because of volume mounting. No need to rebuild!

1. Edit files in `Files/` directory
2. Refresh browser to see changes
3. For configuration changes, clear cache:
   ```bash
   docker-compose exec app php core/artisan config:clear
   ```

## Stopping vs Removing

- **Stop** (`docker-compose stop`): Containers are stopped but not removed. Data persists.
- **Down** (`docker-compose down`): Containers are stopped and removed. Volumes (database) persist.
- **Down with volumes** (`docker-compose down -v`): Everything is removed including database data.

## Benefits of Using Docker

1. ✅ No conflict with XAMPP
2. ✅ Exact PHP 8.3 version
3. ✅ Isolated environment
4. ✅ Easy to share with team
5. ✅ Reproducible setup
6. ✅ Multiple projects with different PHP versions
7. ✅ Quick teardown and rebuild

## Next Steps

1. Access http://localhost:8080 to view your application
2. Use phpMyAdmin at http://localhost:8081 to manage the database
3. Check if the installation wizard appears or if the app is ready to use
4. Import any existing database dumps if needed

## Need Help?

- View logs: `docker-compose logs -f`
- Check running containers: `docker-compose ps`
- Restart everything: `docker-compose restart`
- Open shell: `docker-compose exec app bash`

---

**Note:** Make sure Docker Desktop is running before executing any commands!
