@echo off
echo Starting Research Collaboration Platform...
echo.
echo Starting MySQL...
start cmd /k "cd C:\xampp\mysql\bin && mysqld.exe --console"
timeout /t 5
echo.
echo Starting PHP Server...
echo.
echo Application URL: http://localhost:8080
echo Test Login: test@example.com / password123
echo.
cd C:\xampp\htdocs\research-collab-platform
C:\xampp\php\php.exe -S localhost:8080 -t public
pause