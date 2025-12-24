# Translation System - API-Based Approach

## Overview

The translation system has been changed from a middleware-based auto-translation approach to a more reliable API-based approach that:
- **Does NOT affect design or layout** - translations happen after page render
- **Provides on-demand translation** via REST API
- **Uses Google Translate** (free endpoint) for accuracy
- **Caches results** for 30 days to improve performance
- **Works with existing locale system** - respects language switching

## How It Works

### 1. Locale Selection
- User selects language via `?lang=en` query parameter or language switcher
- SetLocale middleware persists choice in session + 1-year cookie
- Default locale is Indonesian (id)

### 2. API-Based Translation
Instead of translating HTML middleware-style, we now provide a REST API endpoint:

**Endpoint:** `POST /api/translate`

**Request Body:**
```json
{
    "text": "Selamat datang",
    "target": "en"
}
```

**Response:**
```json
{
    "success": true,
    "locale": "en",
    "data": {
        "original": "Selamat datang",
        "translated": "Welcome"
    }
}
```

### 3. Client-Side Translation Library
Included in all pages via `@include('components.client-translator')`, provides:

```javascript
// Translate single text
const result = await translateText('Selamat datang');
console.log(result); // "Welcome"

// Translate multiple texts
const results = await translateTexts(['Hello', 'World']);
console.log(results); // ["Hello", "World"]

// Get current locale
const locale = getCurrentLocale(); // "en" or "id"

// Check if should translate
if (shouldTranslate()) {
    console.log('Page is in English');
}
```

## Usage Examples

### Frontend Pages
Pages maintain their original design and layout. Translation happens **after** page loads via API.

```blade
<!-- No special markup needed - page loads in Indonesian by default -->
<h1>Selamat datang</h1>

<!-- When user selects English (?lang=en), JavaScript can optionally translate -->
<script>
    if (window.shouldTranslate()) {
        // Translate specific elements as needed
        document.querySelector('h1').textContent = await translateText(document.querySelector('h1').textContent);
    }
</script>
```

### Dashboard
Dashboard pages are fully preserved in Indonesian. When English is selected:
1. Page loads in Indonesian
2. User can see `X-Translated` header (if implemented)
3. Optional: Add translation toggles on the page

### Programmatic Translation
In your controllers or views:

```php
// In controller
$translated = app(\App\Services\GoogleTranslateService::class)
    ->translate('Selamat datang', 'en');

// In blade
{{ trans('messages.welcome') ?? 'Selamat datang' }}
```

## Benefits of This Approach

| Aspect | Previous Approach | New Approach |
|--------|------------------|--------------|
| **Design Impact** | High risk - HTML parsing could break layouts | Zero - happens after render |
| **Performance** | Slow - translates all HTML on every request | Fast - API caching + on-demand |
| **Reliability** | Complex regex parsing prone to errors | Simple API call, proven library |
| **Flexibility** | All-or-nothing | Translate what you want, when you want |
| **Maintainability** | Complex middleware logic | Simple REST API + JS utilities |
| **User Experience** | Risk of layout shifts | Stable page load, optional translations |

## Configuration

### Language Switching
Users select language via:
1. Query parameter: `?lang=en` or `?lang=id`
2. Language switcher component (can be added to header)
3. URL path: `/lang/en` route

### Locale Persistence
Locale is saved in:
- Session (during request)
- Cookie (1 year expiration)
- Browser localStorage (optional)

### Caching
Translations are cached for 30 days in Laravel Cache:
- Cache key: `google_translate_` + MD5(text|target_lang)
- Automatic invalidation after 30 days
- Manual clear: `php artisan cache:clear`

## Disabled Features

The following has been **disabled** to prevent layout issues:
- `AutoTranslateResponse` middleware (commented out in bootstrap/app.php)
- Full-page HTML translation via regex parsing

## Testing

### Test the API
```bash
curl -X POST http://localhost:8000/api/translate \
  -H "Content-Type: application/json" \
  -d '{"text": "Selamat datang", "target": "en"}'
```

### Test the Frontend
1. Visit: `http://localhost:8000/?lang=en`
2. Check browser console for JavaScript utility availability
3. Try calling: `console.log(await translateText('Selamat datang'))`

### Check Logs
Monitor translation activity:
```bash
tail -f storage/logs/laravel.log | grep -i translate
```

## Future Enhancements

Possible additions:
1. **Pre-translate key pages** - Translate dashboard on page load for specific locales
2. **Translation cache warming** - Pre-cache common translations on app startup
3. **User preference storage** - Save user's language preference in database
4. **Batch translation** - Load all page text at once for better performance
5. **Crowdsourced translations** - Let users suggest better translations
6. **Manual translation files** - For critical UI strings, maintain manual translation files in `lang/` directories

## Troubleshooting

### Translation returns original text
1. Check Google Translate API is accessible: `curl https://translate.googleapis.com/translate_a/single?...`
2. Check logs for errors: `storage/logs/laravel.log`
3. Verify locale is 'en': Check `getCurrentLocale()` in browser console

### API endpoint not found
1. Clear route cache: `php artisan route:cache` and then `php artisan route:clear`
2. Check route is registered: `php artisan route:list | grep translate`

### Layout changes appear
1. Ensure AutoTranslateResponse middleware is commented out
2. Don't use the old HTML translation approach
3. Use API-based translation instead
