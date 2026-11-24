$env:PATH = "C:\php84\php-ts;" + $env:PATH
Set-Location $PSScriptRoot
Write-Host "Menjalankan Laravel dengan PHP 8.5..." -ForegroundColor Green
php artisan serve
