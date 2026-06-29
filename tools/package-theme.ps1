# Build a clean, installable theme zip: dist/aeon.zip  (contains a single
# top-level "aeon/" folder), ready for WP Admin -> Appearance -> Themes ->
# Add New -> Upload Theme.
#
#   powershell -ExecutionPolicy Bypass -File tools/package-theme.ps1
#
# NOTE: builds the archive manually with forward-slash entry names. The built-in
# Compress-Archive (Windows PowerShell 5.1) writes backslash separators, which
# break theme extraction on Linux WordPress hosts.
$ErrorActionPreference = 'Stop'
Add-Type -AssemblyName System.IO.Compression
Add-Type -AssemblyName System.IO.Compression.FileSystem

$root     = Split-Path -Parent $PSScriptRoot
$themeDir = Join-Path $root 'app\wp-content\themes\aeon'
$distDir  = Join-Path $root 'dist'
$zipPath  = Join-Path $distDir 'aeon.zip'

# Stray top-level items that must never ship inside the theme.
$excludeDirs = @('.git', 'node_modules')

$themeFull = (Resolve-Path $themeDir).Path.TrimEnd('\')
$files = Get-ChildItem -Path $themeDir -Recurse -File -Force | Where-Object {
  $rel = $_.FullName.Substring($themeFull.Length + 1)
  $top = $rel.Split('\')[0]
  ($excludeDirs -notcontains $top) -and ($_.Extension -ne '.log')
}

New-Item -ItemType Directory -Force -Path $distDir | Out-Null
if (Test-Path $zipPath) { Remove-Item $zipPath -Force }

$zip = [System.IO.Compression.ZipFile]::Open($zipPath, [System.IO.Compression.ZipArchiveMode]::Create)
try {
  foreach ($f in $files) {
    $rel = $f.FullName.Substring($themeFull.Length + 1).Replace('\', '/')
    $entryName = "aeon/$rel"   # forward slashes, top-level aeon/ folder
    [System.IO.Compression.ZipFileExtensions]::CreateEntryFromFile($zip, $f.FullName, $entryName) | Out-Null
  }
} finally {
  $zip.Dispose()
}

Write-Host "Built $zipPath ($($files.Count) files)"
Write-Host "Upload it via WP Admin -> Appearance -> Themes -> Add New -> Upload Theme."
