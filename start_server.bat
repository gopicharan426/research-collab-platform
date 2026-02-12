@echo off
echo Starting Research Collaboration Platform...
echo.
echo Database Setup: http://localhost:8080/setup.php
echo Application: http://localhost:8080
echo.
echo Test Account: test@example.com / password123
echo.
cd /d C:\xampp\htdocs\research-collab-platform
C:\xampp\php\php.exe -S localhost:8080 -t public
pause