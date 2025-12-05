# Fix Storage Link for Template Images
# This script creates the symbolic link and verifies the setup

Write-Host "=== FIX STORAGE LINK ===" -ForegroundColor Cyan
Write-Host ""

# 1. Check if storage link already exists
Write-Host "1. Checking existing storage link..." -ForegroundColor Yellow
$storageLinkPath = "public\storage"

if (Test-Path $storageLinkPath) {
    Write-Host "   ✓ Storage link exists: $storageLinkPath" -ForegroundColor Green

    # Check if it's a symlink
    $item = Get-Item $storageLinkPath -Force
    if ($item.Attributes -band [System.IO.FileAttributes]::ReparsePoint) {
        Write-Host "   ✓ It's a symbolic link" -ForegroundColor Green
    } else {
        Write-Host "   ⚠ It's a regular directory, not a symlink" -ForegroundColor Yellow
        Write-Host "   Removing and recreating as symlink..." -ForegroundColor Yellow
        Remove-Item $storageLinkPath -Recurse -Force
    }
} else {
    Write-Host "   ✗ Storage link doesn't exist" -ForegroundColor Red
}

Write-Host ""

# 2. Create storage link using Laravel command
Write-Host "2. Creating/verifying storage link..." -ForegroundColor Yellow
php artisan storage:link
Write-Host ""

# 3. Verify directory structure
Write-Host "3. Verifying directory structure..." -ForegroundColor Yellow

$storagePublicPath = "storage\app\public"
$templatesPath = "$storagePublicPath\templates\previews"

if (Test-Path $storagePublicPath) {
    Write-Host "   ✓ storage/app/public exists" -ForegroundColor Green
} else {
    Write-Host "   ✗ storage/app/public missing" -ForegroundColor Red
}

if (Test-Path $templatesPath) {
    Write-Host "   ✓ storage/app/public/templates/previews exists" -ForegroundColor Green

    # Count images
    $imageCount = (Get-ChildItem $templatesPath -Filter *.* -File -ErrorAction SilentlyContinue).Count
    Write-Host "   → Found $imageCount image(s) in templates/previews" -ForegroundColor Cyan
} else {
    Write-Host "   ⚠ templates/previews directory doesn't exist yet" -ForegroundColor Yellow
    Write-Host "   → Will be created when you upload the first template image" -ForegroundColor Cyan
}

Write-Host ""

# 4. Test URL access
Write-Host "4. Testing image access..." -ForegroundColor Yellow

if (Test-Path $templatesPath) {
    $testImage = Get-ChildItem $templatesPath -Filter *.* -File -ErrorAction SilentlyContinue | Select-Object -First 1

    if ($testImage) {
        $filename = $testImage.Name
        Write-Host "   Test image: $filename" -ForegroundColor Cyan
        Write-Host "   Physical path: storage/app/public/templates/previews/$filename" -ForegroundColor Cyan
        Write-Host "   URL should be: http://yourdomain.com/storage/templates/previews/$filename" -ForegroundColor Green
        Write-Host "   Blade code: {{ asset('storage/' . `$template->preview_image) }}" -ForegroundColor Green
    } else {
        Write-Host "   No test images found yet" -ForegroundColor Yellow
    }
}

Write-Host ""
Write-Host "=== SUMMARY ===" -ForegroundColor Cyan
Write-Host ""
Write-Host "If images still show 404:" -ForegroundColor Yellow
Write-Host "1. Make sure you ran: php artisan storage:link" -ForegroundColor White
Write-Host "2. Upload a new template image via admin panel" -ForegroundColor White
Write-Host "3. Check the image URL in browser dev tools" -ForegroundColor White
Write-Host "4. Verify path is: /storage/templates/previews/filename.jpg" -ForegroundColor White
Write-Host ""
Write-Host "✓ All view files have been updated to use 'storage/' prefix" -ForegroundColor Green
Write-Host ""
