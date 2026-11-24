@echo off
set "PATH=C:\php84\php-ts;%PATH%"
cd /d "%~dp0"
echo Menjalankan Laravel dengan PHP 8.5...
php artisan serve
pause
