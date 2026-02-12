@echo off
echo Creating deployment package for free hosting...
echo.

mkdir free_hosting_package
xcopy public free_hosting_package /E /I /Y
xcopy app free_hosting_package\app /E /I /Y  
copy database\schema.sql free_hosting_package\
copy FREE_HOSTING.md free_hosting_package\

echo.
echo Package created in 'free_hosting_package' folder
echo Upload all contents to your free hosting public_html/
echo.
pause