@echo off
echo ========================================
echo Jordan University Student Portal Setup
echo ========================================
echo.

echo Installing Composer dependencies...
composer install
if %errorlevel% neq 0 (
    echo Error: Failed to install Composer dependencies
    pause
    exit /b 1
)

echo.
echo Generating application key...
php artisan key:generate
if %errorlevel% neq 0 (
    echo Error: Failed to generate application key
    pause
    exit /b 1
)

echo.
echo Running database migrations...
php artisan migrate
if %errorlevel% neq 0 (
    echo Error: Failed to run migrations
    echo Please check your database configuration in .env file
    pause
    exit /b 1
)

echo.
echo Seeding database with sample data...
php artisan db:seed
if %errorlevel% neq 0 (
    echo Error: Failed to seed database
    pause
    exit /b 1
)

echo.
echo ========================================
echo Setup completed successfully!
echo ========================================
echo.
echo You can now start the development server with:
echo php artisan serve
echo.
echo Login credentials:
echo Student ID: 20210001
echo Password: password123
echo.
pause