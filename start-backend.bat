@echo off
echo Starting Laravel Backend Server...
echo.
echo Make sure you have:
echo 1. PHP installed
echo 2. Composer installed
echo 3. Database configured
echo.
cd backend
php artisan serve --host=127.0.0.1 --port=8000
pause
