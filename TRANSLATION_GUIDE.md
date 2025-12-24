# Google Translate Integration Guide

## Overview
This application uses **Google Translate API** for automatic website translation. Indonesian is set as the **default language**, and English is available as a secondary language.

## Features
- ✅ Free Google Translate API (no API key required)
- ✅ Automatic translation on demand
- ✅ 30-day caching for performance
- ✅ Language switcher in dashboard navbar
- ✅ Query parameter-based language switching (`?lang=en` or `?lang=id`)
- ✅ Session-based locale persistence

## Architecture

### Core Components

#### 1. **GoogleTranslateService** (`app/Services/GoogleTranslateService.php`)
The main service handling translation logic.

```php
use App\Services\GoogleTranslateService;

$translator = app(GoogleTranslateService::class);

// Translate a single text
$english = $translator->translate('Selamat datang', 'en');

// Translate to current locale
$translated = $translator->translate('Halo dunia', app()->getLocale());
```

**Methods:**
- `translate(string $text, string $targetLang = 'en'): string` - Translates text, caches result 30 days
- `translateArray(array $texts, string $targetLang = 'en'): array` - Bulk translation
- `setCacheDuration(int $days): self` - Customize cache duration

#### 2. **Blade Directives** (registered in `AppServiceProvider`)

Use these directives in your Blade templates:

```blade
<!-- Short syntax (recommended) -->
<h1>@t('Selamat datang')</h1>

<!-- Full directive syntax -->
<p>@translate('Ini adalah halaman beranda kami')</p>

<!-- With variables -->
<p>@t("Hello {{ $username }}")</p>
```

#### 3. **SetLocale Middleware** (`app/Http/Middleware/SetLocale.php`)
- Runs on all web requests
- Sets application locale based on:
  1. Query parameter (`?lang=id` or `?lang=en`)
  2. Session value
  3. Cookie value
  4. Default locale ('id')

#### 4. **Language Switcher** (`resources/views/partials/language-switcher.blade.php`)
- Dropdown in dashboard navbar (top-right)
- Shows current language with flag
- Links to switch language using `?lang=` query parameter
- Flags: 🇮🇩 Indonesian, 🇺🇸 English

## How to Use

### Adding Translations to Blade Templates

**Before (no translation):**
```blade
<h1>Welcome to our platform</h1>
<p>Create your professional resume and apply for jobs easily.</p>
<button>Start Now</button>
```

**After (with translations):**
```blade
<h1>@t('Selamat datang di platform kami')</h1>
<p>@t('Buat resume profesional Anda dan lamar pekerjaan dengan mudah.')</p>
<button>@t('Mulai Sekarang')</button>
```

### Recommended Pattern for Multi-Line Content
```blade
<div class="welcome-section">
    <h1>@t('Selamat datang')</h1>
    <p>@t('Kami siap membantu Anda menemukan pekerjaan impian')</p>
    
    {{-- Form inputs and buttons --}}
    <input placeholder="@t('Masukkan nama Anda')" />
    <button class="btn">@t('Kirim')</button>
</div>
```

## Configuration

### Default Locale
Set in `config/app.php`:
```php
'locale' => env('APP_LOCALE', 'id'), // Indonesian by default
'fallback_locale' => 'en',
```

### Cache Duration
The translation cache is set to 30 days by default. To customize:

```php
// In a controller or service
app(GoogleTranslateService::class)->setCacheDuration(7); // 7 days
```

### Supported Languages
Currently supported:
- `id` - Indonesian (default)
- `en` - English (secondary)

To add more languages, update:
1. `SetLocale` middleware validation
2. Language switcher in `language-switcher.blade.php`
3. Add corresponding flags from https://flagcdn.com/

## Language Switching

### Via URL Query Parameter
```
Current page URL: /dashboard
Switch to English: /dashboard?lang=en
Switch to Indonesian: /dashboard?lang=id
```

### Via Dashboard Switcher
Click the language flag in the top-right navbar to switch languages.

### Session Persistence
Once a user selects a language, it persists across pages via session storage.

## API Details

### Google Translate API Endpoint
```
https://translate.googleapis.com/translate_a/single
```

**Parameters:**
- `client=gtx` - Client identifier (free API)
- `sl=id` - Source language
- `tl=en` - Target language
- `dt=t` - Request type (translation)
- `q=text` - Text to translate

**Response:** JSON array containing translated segments

### Caching
- Cache key format: `google_translate_md5($text.$targetLang)`
- Duration: 30 days (configurable)
- Storage: Laravel Cache (file/Redis/database)

## Examples

### Example 1: Dashboard Welcome Message
```blade
<div class="card border-0">
    <div class="card-body">
        <h4>@t('Selamat datang, Admin!')</h4>
        <p>@t('Berikut adalah ringkasan aktivitas platform Anda hari ini.')</p>
        <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
            @t('Tambah Pengguna Baru')
        </a>
    </div>
</div>
```

### Example 2: Form with Translations
```blade
<form method="POST" action="/search">
    <div class="form-group">
        <label>@t('Cari Pekerjaan')</label>
        <input type="text" class="form-control" placeholder="@t('Ketik nama pekerjaan...')">
    </div>
    <button type="submit" class="btn btn-primary">@t('Cari')</button>
</form>
```

### Example 3: Error Messages
```blade
@if ($errors->any())
    <div class="alert alert-danger">
        <strong>@t('Terjadi Kesalahan!')</strong>
        <ul>
            @foreach ($errors->all() as $error)
                <li>@t($error)</li>
            @endforeach
        </ul>
    </div>
@endif
```

## Performance Considerations

1. **Caching**: All translations are cached for 30 days. First request translates, subsequent requests use cache.
2. **API Rate Limiting**: Google's free API has no official rate limits but use responsibly
3. **Network Requests**: Minimize translations by using cache effectively
4. **Batch Translations**: Use `translateArray()` for multiple texts

## Troubleshooting

### Translations Not Appearing
**Issue:** Text not being translated when switching to English

**Solution:**
1. Check browser cache - clear and refresh
2. Verify locale is set to 'en': `{{ app()->getLocale() }}`
3. Check if text is wrapped in `@t()` directive

### API Connection Error
**Issue:** "Error calling Google Translate API"

**Solution:**
1. Check internet connection
2. Verify firewall allows access to `translate.googleapis.com`
3. Check server logs: `storage/logs/laravel.log`
4. Original text will be returned if translation fails (fallback)

### Cache Not Working
**Issue:** Translations showing stale content

**Solution:**
```bash
# Clear translation cache
php artisan cache:clear

# Or for specific cache driver
php artisan cache:clear --tags=translation
```

## Development Tips

### Testing Translations
```blade
<!-- Display current locale -->
Current Locale: {{ app()->getLocale() }}

<!-- Check if English -->
@if(app()->getLocale() === 'en')
    <p>Currently in English mode</p>
@else
    <p>Dalam bahasa Indonesia</p>
@endif
```

### Debugging Translation Service
```php
// In a controller
$service = app(\App\Services\GoogleTranslateService::class);
$result = $service->translate('Halo', 'en');
dump($result); // Check translation output
```

## Future Enhancements

- [ ] Add browser language detection (Accept-Language header)
- [ ] Support more languages (Spanish, French, German, etc.)
- [ ] Admin panel for managing translations
- [ ] Override specific translations in database
- [ ] Batch translation for entire page
- [ ] Translation statistics and monitoring

## Common Issues & Solutions

| Issue | Solution |
|-------|----------|
| Text not translating | Wrap in `@t('text')` directive |
| Translations cached too long | Clear cache: `php artisan cache:clear` |
| Language not persisting | Check if session enabled, cookies allowed |
| API errors in logs | Check internet, firewall rules |
| Repeated translations | Cache is working (normal) |

## Support

For issues or questions about the translation system, check:
1. Laravel logs: `storage/logs/laravel.log`
2. Google Translate API status
3. Browser console for JavaScript errors
