@echo off
cd /d d:\.project\BakeryLaravel
echo === Checking Migration Status ===
php artisan migrate:status
echo.
echo === Checking App Information ===
php artisan about
echo.
echo === Creating images directory ===
if not exist storage\app\public\images mkdir storage\app\public\images
if exist storage\app\public\images (
    echo Images directory created successfully
) else (
    echo Images directory already exists
)
echo.
echo === Listing storage/app/public contents ===
dir storage\app\public
echo.
echo === Verification Complete ===
