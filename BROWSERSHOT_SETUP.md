# Browsershot Setup for Automatic Template Previews

## What This Does
Automatically generates preview images of resume templates when admin adds/updates them using headless Chrome.

## Installation Steps

### 1. Install Browsershot Package
```bash
composer install
```
This will install `spatie/browsershot` which is already added to composer.json.

### 2. Install Node.js and Puppeteer (Server Requirements)

**On Ubuntu/Debian:**
```bash
# Install Node.js (if not already installed)
curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
sudo apt-get install -y nodejs

# Install Puppeteer globally
sudo npm install -g puppeteer

# Install Chrome dependencies
sudo apt-get install -y \
    ca-certificates \
    fonts-liberation \
    libappindicator3-1 \
    libasound2 \
    libatk-bridge2.0-0 \
    libatk1.0-0 \
    libcups2 \
    libdbus-1-3 \
    libgdk-pixbuf2.0-0 \
    libnspr4 \
    libnss3 \
    libx11-xcb1 \
    libxcomposite1 \
    libxdamage1 \
    libxrandr2 \
    xdg-utils \
    libgbm1
```

**On CPanel/Shared Hosting:**
```bash
# Install Node.js via cPanel Node.js selector
# Then run:
npm install -g puppeteer --unsafe-perm=true --allow-root
```

**On Windows (Local Development):**
```powershell
# Install Node.js from nodejs.org
# Then run:
npm install -g puppeteer
```

### 3. Test Browsershot
```bash
php artisan tinker
```
Then run:
```php
\Spatie\Browsershot\Browsershot::html('<h1>Hello World</h1>')
    ->save('test.png');
```

If it creates `test.png`, you're good to go!

### 4. Create Previews Directory
```bash
mkdir -p public/uploads/templates/previews
chmod 755 public/uploads/templates/previews
```

## How It Works

When an admin:
1. **Creates a new template** - Preview automatically generated
2. **Updates a template** - New preview automatically generated
3. Preview saved to: `public/uploads/templates/previews/{slug}-preview-{timestamp}.png`

## Troubleshooting

### Error: "Could not find Chrome"
```bash
# Install Chrome manually
wget https://dl.google.com/linux/direct/google-chrome-stable_current_amd64.deb
sudo dpkg -i google-chrome-stable_current_amd64.deb
sudo apt-get install -f
```

### Error: "Failed to launch the browser"
```bash
# Run with --no-sandbox
# Edit the generateTemplatePreview method to add:
->noSandbox()
```

### Permissions Error
```bash
chmod -R 755 public/uploads
chown -R www-data:www-data public/uploads  # or your web server user
```

### Memory Issues
If generating large previews fails, increase PHP memory:
```php
// In config/app.php or .env
ini_set('memory_limit', '512M');
```

## Fallback Behavior

If Browsershot fails for any reason:
- The template still saves successfully
- Preview will be `null` in database
- Admin can regenerate preview later
- Error logged to `storage/logs/laravel.log`

## Manual Preview Regeneration

To regenerate previews for existing templates:
```php
php artisan tinker

// Regenerate for specific template
$template = App\Models\Template::find(1);
$controller = new App\Http\Controllers\Admin\TemplateController();
$reflection = new ReflectionClass($controller);
$method = $reflection->getMethod('generateTemplatePreview');
$method->setAccessible(true);
$preview = $method->invoke($controller, $template);
$template->update(['preview_image' => $preview]);

// Or regenerate all
App\Models\Template::all()->each(function($t) use ($controller, $method) {
    $preview = $method->invoke($controller, $t);
    $t->update(['preview_image' => $preview]);
});
```

## Performance

- Preview generation takes ~2-3 seconds per template
- Happens in background during template save
- Does NOT block admin interface
- Generated images are ~200KB each

## Requirements Summary

✅ PHP 8.2+
✅ Node.js 16+
✅ Puppeteer
✅ Chrome/Chromium browser
✅ 512MB RAM minimum
✅ Write permissions on `public/uploads`
