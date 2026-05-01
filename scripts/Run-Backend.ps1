$projectRoot = Split-Path -Parent $PSScriptRoot
$backendPath = Join-Path $projectRoot 'backend'
$phpCommand = Get-Command php -ErrorAction SilentlyContinue

if ($phpCommand) {
  $php = $phpCommand.Source
} else {
  $localPhp = Join-Path (Split-Path -Parent $projectRoot) '.tools\php\php.exe'

  if (-not (Test-Path $localPhp)) {
    throw 'PHP executable was not found. Install PHP or place it under .tools\php\php.exe.'
  }

  $php = $localPhp
}

Set-Location $backendPath
& $php -S 127.0.0.1:8003 -t public
