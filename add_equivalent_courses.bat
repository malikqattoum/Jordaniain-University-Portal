@echo off
echo ========================================
echo Adding Equivalent Courses to Students
echo ========================================
echo.

echo Adding equivalent courses for:
echo - KHALID SAEDAN HD AL-MEJABA
echo - IBRAHIM JASSIM I H AL-QATTAN  
echo - YOUSEF ABDULAZIZ A A AL-MUHAIZA
echo.

php artisan db:seed --class=StudentEquivalentCoursesSeeder

if %errorlevel% neq 0 (
    echo Error: Failed to add equivalent courses
    pause
    exit /b 1
)

echo.
echo ========================================
echo Equivalent courses added successfully!
echo ========================================
echo.
echo Total equivalent courses per student: 24
echo Total credit hours per student: 72.0
echo.
echo The equivalent courses table is now available
echo in the student dashboard for all three students.
echo.
pause