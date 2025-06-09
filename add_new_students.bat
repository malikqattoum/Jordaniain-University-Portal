@echo off
echo ========================================
echo Adding New Students to Database
echo ========================================
echo.

echo Adding Ibrahim Jasim Ibrahim Hussein Al-Qattan...
echo Adding Youssef Abdul Aziz Ahmed Abdul Aziz Al-Mahizaa...
echo.

php artisan db:seed --class=NewStudentsSeeder

if %errorlevel% neq 0 (
    echo Error: Failed to add new students
    pause
    exit /b 1
)

echo.
echo ========================================
echo New students added successfully!
echo ========================================
echo.
echo New login credentials:
echo.
echo Student 1: Ibrahim Al-Qattan
echo Student ID: 20240001
echo Password: password123
echo.
echo Student 2: Youssef Al-Mahizaa  
echo Student ID: 20240002
echo Password: password123
echo.
pause