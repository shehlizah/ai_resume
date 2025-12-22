@echo off
cd /d C:\Users\dell\ai_resume
php artisan schedule:run >> NUL 2>&1
