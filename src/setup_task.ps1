# PowerShell script to schedule a daily task for cron.php
$TaskName = "XKCDDailyComic"
$TaskDescription = "Runs cron.php daily to send XKCD comics to subscribers"
$ScriptPath = Join-Path $PSScriptRoot "cron.php"
$PhpPath = "C:\xampp\php\php.exe" # Adjust to your PHP executable path
$Action = New-ScheduledTaskAction -Execute $PhpPath -Argument $ScriptPath
$Trigger = New-ScheduledTaskTrigger -Daily -At "12:00 AM"
$Settings = New-ScheduledTaskSettingsSet -AllowStartIfOnBatteries -DontStopIfGoingOnBatteries

# Check if task already exists
$ExistingTask = Get-ScheduledTask -TaskName $TaskName -ErrorAction SilentlyContinue
if (-not $ExistingTask) {
    Register-ScheduledTask -TaskName $TaskName -Action $Action -Trigger $Trigger -Description $TaskDescription -Settings $Settings
    Write-Host "Scheduled task '$TaskName' created to run daily at midnight."
} else {
    Write-Host "Scheduled task '$TaskName' already exists."
}