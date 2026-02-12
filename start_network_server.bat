@echo off
echo Making server accessible to friends on your network...
echo.
echo Your friends can access at: http://YOUR_IP_ADDRESS:8080
echo.
echo To find your IP: ipconfig
echo Example: http://192.168.1.100:8080
echo.
cd /d C:\xampp\htdocs\research-collab-platform
C:\xampp\php\php.exe -S 0.0.0.0:8080 -t public
pause