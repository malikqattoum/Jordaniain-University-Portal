@echo off
echo ========================================
echo Adding Khalid Student to Database
echo ========================================
echo.

echo Adding خالد سعيدان حسن ظافر العجبة...
echo Second Semester 2024/2025
echo.

php artisan db:seed --class=KhalidStudentSeeder

if %errorlevel% neq 0 (
    echo Error: Failed to add Khalid student
    pause
    exit /b 1
)

echo.
echo ========================================
echo Khalid student added successfully!
echo ========================================
echo.
echo New login credentials:
echo.
echo Student: Khalid Saeedan Hassan Dhafer Al-Ajaba
echo Student ID: 20240003
echo Password: password123
echo.
echo Academic Performance:
echo - Semester GPA: 2.30
echo - Status: Regular (منتظم)
echo - Credit Hours: 21
echo.
pause