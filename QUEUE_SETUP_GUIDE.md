# Automatic Queue Processing Setup

## What This Does
The AI candidate matching runs automatically in the background with a 30-minute delay after job posting. No manual `php artisan queue:work` needed!

## How It Works
- When a company posts a job, the matching job is queued with 30-minute delay
- Laravel's scheduler runs `queue:work --stop-when-empty` every minute
- Windows Task Scheduler executes the Laravel scheduler every minute
- Matches are processed automatically and employers get notified

## Windows Task Scheduler Setup (One-Time)

### Option 1: Quick Setup (Run this command as Administrator)
```powershell
schtasks /create /tn "Laravel Scheduler" /tr "C:\Users\dell\ai_resume\task-scheduler.bat" /sc minute /mo 1 /ru "SYSTEM"
```

### Option 2: Manual Setup via GUI
1. Open **Task Scheduler** (search in Start menu)
2. Click **Create Basic Task**
3. Name: `Laravel Scheduler`
4. Trigger: **Daily**, start at any time
5. Action: **Start a program**
   - Program: `C:\Users\dell\ai_resume\task-scheduler.bat`
6. In final screen, check "Open Properties dialog"
7. In **Triggers** tab:
   - Edit the trigger
   - Click "Repeat task every" → **1 minute**
   - Duration: **Indefinitely**
8. In **Settings** tab:
   - Uncheck "Stop the task if it runs longer than"
9. Click **OK**

### Verify It's Running
```powershell
# Check scheduled task status
schtasks /query /tn "Laravel Scheduler"

# Check queue logs
php artisan queue:failed

# Monitor in real-time (optional)
Get-Content storage\logs\laravel.log -Wait
```

## Production Alternative
For production hosting (cPanel, Plesk, etc.), add this cron job:
```
* * * * * cd /path/to/project && php artisan schedule:run >> /dev/null 2>&1
```

## Troubleshooting
- **Jobs not processing?** Check Task Scheduler is running
- **Permission errors?** Run Task Scheduler as Administrator
- **Want instant testing?** Temporarily change Job model to use `dispatchSync()` instead of `dispatch()->delay()`
