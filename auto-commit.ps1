# Auto-commit script for fruit2web project
# Run this script periodically to capture changes via git

param(
    [string]$Message = "Auto-commit: $(Get-Date -Format 'yyyy-MM-dd HH:mm:ss')"
)

$projectRoot = Split-Path -Parent $MyInvocation.MyCommand.Path
$logFile = Join-Path $projectRoot "auto-commit.log"

function Log-Message {
    param([string]$Text)
    $timestamp = Get-Date -Format "yyyy-MM-dd HH:mm:ss"
    $logEntry = "[$timestamp] $Text"
    Add-Content -Path $logFile -Value $logEntry
    Write-Host $logEntry
}

try {
    Push-Location $projectRoot
    
    Log-Message "Starting auto-commit process..."
    
    # Check if git repo exists
    if (-not (Test-Path .git)) {
        Log-Message "ERROR: Not a git repository!"
        exit 1
    }
    
    # Configure git user (if not already configured)
    $gitUser = git config user.email 2>$null
    if ([string]::IsNullOrWhiteSpace($gitUser)) {
        Log-Message "Configuring git user..."
        git config user.email "auto-commit@fruit2web.local"
        git config user.name "Auto Commit"
    }
    
    # Check git status
    $gitStatus = git status --porcelain
    
    if ([string]::IsNullOrWhiteSpace($gitStatus)) {
        Log-Message "No changes detected. Nothing to commit."
        exit 0
    }
    
    # Count changes
    $changeCount = ($gitStatus | Measure-Object -Line).Lines
    Log-Message "Detected $changeCount file(s) with changes"
    
    # Show what will be committed
    Log-Message "Changed files:"
    $gitStatus | ForEach-Object { Log-Message "  $_" }
    
    # Stage all changes
    Log-Message "Staging changes..."
    git add -A
    
    # Commit changes
    Log-Message "Committing with message: $Message"
    git commit -m $Message
    
    # Get commit hash
    $commitHash = git rev-parse --short HEAD
    Log-Message "Commit successful! Hash: $commitHash"
    Log-Message "---"
    
}
catch {
    Log-Message "ERROR: $_"
    exit 1
}
finally {
    Pop-Location
}
