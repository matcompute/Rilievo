$projectRoot = Split-Path -Parent $PSScriptRoot
$frontendPath = Join-Path $projectRoot 'frontend'

Set-Location $frontendPath
npm.cmd start
