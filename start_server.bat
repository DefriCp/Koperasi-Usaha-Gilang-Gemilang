@echo off
REM Ganti path ini ke folder project Laravel kamu
cd /d "D:\Project Hanip\KuGG"

echo Menjalankan Laravel server di port 8000...
start cmd /k "php artisan serve --host=127.0.0.1 --port=8000"

timeout /t 5 >nul

echo Menjalankan ngrok tunnel di port 8000...
start cmd /k "ngrok http 8000"

echo =======================================
echo Laravel + Ngrok sudah jalan.
echo Jangan tutup jendela ini agar tetap aktif.
echo =======================================
pause
