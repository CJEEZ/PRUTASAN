# PowerShell script to create a Windows Task Scheduler job for auto-commits
# Run this as Administrator

$taskName = "Fruit2Web-AutoCommit"
$projectRoot = "c:\Xamppp\htdocs\fruit2web"
$scriptPath = Join-Path $projectRoot "auto-commit.ps1"

# Task action
$taskAction = New-ScheduledTaskAction `
    -Execute "powershell.exe" `
    -Argument "-NoProfile -ExecutionPolicy Bypass -File `"$scriptPath`""

# Task trigger - Run every 6 hours
$taskTrigger = New-ScheduledTaskTrigger -RepetitionInterval (New-TimeSpan -Hours 6) -Once -At (Get-Date)

# Task settings
$taskSettings = New-ScheduledTaskSettingsSet `
    -AllowStartIfOnBatteries `
    -DontStopIfGoingOnBatteries `
    -RunWithoutNetwork `
    -MultipleInstances IgnoreNew

# Register the task
$principal = New-ScheduledTaskPrincipal `
    -UserID "$env:COMPUTERNAME\$env:USERNAME" `
    -RunLevel Highest

Register-ScheduledTask `
    -TaskName $taskName `
    -Action $taskAction `
    -Trigger $taskTrigger `
    -Settings $taskSettings `
    -Principal $principal `
    -Force

Write-Output "✓ Scheduled task created: $taskName"
Write-Output "  Script: $scriptPath"
Write-Output "  Interval: Every 6 hours"
Write-Output ""
Write-Output "To manage the task:"
Write-Output "  - View: Get-ScheduledTask -TaskName '$taskName'"
Write-Output "  - Run now: Start-ScheduledTask -TaskName '$taskName'"
Write-Output "  - Delete: Unregister-ScheduledTask -TaskName '$taskName' -Confirm:`$false"
