@echo off
REM Windows batch script to run the 1C SOAP stock synchronization.
REM Auto-generated for Skembedjis.

cd /d "c:\Users\Georg\Desktop\Portfolio\Skembedjis"
echo [1/2] Connecting to 1C SOAP and generating items.json...
"C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe" soap.php

echo [2/2] Synchronizing items.json with website database...
"C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe" sync_products.php --source=json

echo Sync process completed!
