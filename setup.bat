@echo off
echo ===============================================
echo   Jattik Courier - Docker Setup Script
echo ===============================================
echo.

REM Check if Docker is running
docker info >nul 2>&1
if errorlevel 1 (
    echo ERROR: Docker is not running!
    echo Please start Docker Desktop and try again.
    pause
    exit /b 1
)

echo [1/5] Building Docker containers...
docker-compose build

echo.
echo [2/5] Starting containers...
docker-compose up -d

echo.
echo [3/5] Waiting for MySQL to be ready...
timeout /t 10 /nobreak >nul

echo.
echo [4/5] Generating Laravel application key...
docker-compose exec -T app php core/artisan key:generate

echo.
echo [5/5] Running database migrations...
docker-compose exec -T app php core/artisan migrate --force

echo.
echo ===============================================
echo   Setup Complete!
echo ===============================================
echo.
echo Your application is now running at:
echo   - Application: http://localhost:8080
echo   - phpMyAdmin:  http://localhost:8081
echo.
echo Database credentials:
echo   - Host: mysql (from app container) or localhost:3306 (from host)
echo   - Database: courierlab
echo   - Username: root
echo   - Password: root
echo.
echo To stop the containers:    docker-compose stop
echo To start the containers:   docker-compose start
echo To restart the containers: docker-compose restart
echo To view logs:              docker-compose logs -f app
echo To open shell:             docker-compose exec app bash
echo.
pause
