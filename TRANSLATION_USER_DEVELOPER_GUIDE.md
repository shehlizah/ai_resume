# Translation System - User & Developer Guide

## 🌐 For Website Users

### How to Switch Languages

#### Option 1: Using Language Selector in Dashboard
1. Log into your dashboard
2. Look for the **flag icon** in the top-right corner (next to your profile picture)
3. Click the dropdown arrow next to the flag
4. Select your preferred language:
   - 🇮🇩 **Bahasa Indonesia** (Default - Indonesian)
   - 🇺🇸 **English** (English)
5. Page will automatically translate!

#### Option 2: Using URL
Add `?lang=en` or `?lang=id` to any URL:
```
Current page: https://yoursite.com/dashboard
Switch to English: https://yoursite.com/dashboard?lang=en
Switch to Indonesian: https://yoursite.com/dashboard?lang=id
```

### Features
- 🔄 Language preference is **saved in your session**
- 🚀 Stays the same when you navigate to other pages
- 💾 Works on any page with the `@t()` directive
- 📱 Mobile-friendly language switcher

---

## 👨‍💻 For Developers

### Quick Integration (30 Seconds)

**Step 1: Identify Text**
```blade
<h1>Selamat datang</h1>
```

**Step 2: Wrap with @t()**
```blade
<h1>@t('Selamat datang')</h1>
```

**Step 3: Test**
- Default: Shows "Selamat datang" ✅
- With `?lang=en`: Shows "Welcome" ✅

### Where Everything Is

```
app/
  Services/
    GoogleTranslateService.php    ← API integration
  Providers/
    AppServiceProvider.php         ← Blade directives
  Http/Middleware/
    SetLocale.php                  ← Locale handling
    
resources/views/
  partials/
    language-switcher.blade.php   ← UI component
    
config/
  app.php                          ← Default locale: 'id'
```

### Code Examples

#### Example 1: Page Title
```blade
<!-- In controller -->
$pageTitle = 'Resume Saya';

<!-- In blade -->
<h1>@t($pageTitle)</h1>
```

#### Example 2: Navigation Menu
```blade
<nav>
  <a href="/jobs">@t('Cari Pekerjaan')</a>
  <a href="/resumes">@t('Resume Saya')</a>
  <a href="/interviews">@t('Wawancara')</a>
</nav>
```

#### Example 3: Form with Multiple Fields
```blade
<form>
  <label>@t('Nama Lengkap')</label>
  <input placeholder="@t('Masukkan nama Anda')" />
  
  <label>@t('Email')</label>
  <input placeholder="@t('Masukkan email Anda')" />
  
  <button type="submit">@t('Kirim')</button>
</form>
```

#### Example 4: Messages
```blade
<!-- Success -->
@if(session('success'))
  <div class="alert alert-success">
    @t('Berhasil disimpan!')
  </div>
@endif

<!-- Error -->
@if($errors->any())
  <div class="alert alert-danger">
    @t('Terjadi kesalahan. Silakan coba lagi.')
  </div>
@endif
```

### Translation Flow

```
┌─────────────────────────────────────────────────┐
│ User Request (with or without ?lang=en)          │
└────────────────┬────────────────────────────────┘
                 │
                 ▼
┌─────────────────────────────────────────────────┐
│ SetLocale Middleware                             │
│ • Checks ?lang parameter                         │
│ • Checks session/cookie                          │
│ • Sets app()->setLocale('en' or 'id')            │
└────────────────┬────────────────────────────────┘
                 │
                 ▼
┌─────────────────────────────────────────────────┐
│ Blade Template Renders                           │
│ • Encounters @t('Indonesian text')               │
└────────────────┬────────────────────────────────┘
                 │
                 ▼
            ┌────┴─────┐
            │           │
      locale='id'  locale='en'
            │           │
            ▼           ▼
        ┌───────┐  ┌──────────────┐
        │Return │  │Check Cache?  │
        │ orig. │  └──────┬───────┘
        │text  │         │
        └───────┘    ┌────┴─────┐
                     │           │
                   FOUND    NOT FOUND
                     │           │
                     ▼           ▼
                  CACHE    Call Google API
                     │           │
                     └─────┬─────┘
                           │
                           ▼
                    ┌──────────────┐
                    │Return Eng.   │
                    │text & Cache  │
                    │(30 days)     │
                    └──────────────┘
```

### Common Integration Patterns

#### Pattern 1: Static Pages
```blade
<section>
  <h2>@t('Fitur Utama')</h2>
  <p>@t('Aplikasi kami menyediakan berbagai fitur untuk membantu Anda.')
</p>
  <button>@t('Pelajari Lebih Lanjut')</button>
</section>
```

#### Pattern 2: Dynamic Lists
```blade
<ul>
  @foreach($items as $item)
    <li>
      <h4>{{ $item->name }}</h4>
      <p>@t('Deskripsi: ' . $item->description)</p>
    </li>
  @endforeach
</ul>
```

#### Pattern 3: Tables
```blade
<table>
  <thead>
    <tr>
      <th>@t('Kolom 1')</th>
      <th>@t('Kolom 2')</th>
      <th>@t('Aksi')</th>
    </tr>
  </thead>
  <tbody>
    @foreach($rows as $row)
      <tr>
        <td>{{ $row->col1 }}</td>
        <td>{{ $row->col2 }}</td>
        <td>
          <a href="#">@t('Edit')</a>
          <a href="#">@t('Hapus')</a>
        </td>
      </tr>
    @endforeach
  </tbody>
</table>
```

#### Pattern 4: Modal Dialogs
```blade
<div class="modal" id="confirmModal">
  <div class="modal-header">
    <h5>@t('Konfirmasi Aksi')</h5>
  </div>
  <div class="modal-body">
    <p>@t('Apakah Anda yakin ingin menghapus item ini?')</p>
  </div>
  <div class="modal-footer">
    <button class="btn btn-secondary">@t('Batal')</button>
    <button class="btn btn-danger">@t('Hapus')</button>
  </div>
</div>
```

### Testing Translations

#### Test 1: Verify Translation Works
```blade
<!-- In any blade file -->
<p>Current Locale: {{ app()->getLocale() }}</p>

<!-- Then test -->
http://yoursite.com/page           ← Shows 'id', text in Indonesian
http://yoursite.com/page?lang=en  ← Shows 'en', text in English
```

#### Test 2: Check Cache Working
```blade
<!-- First request -->
@t('Buka browser console')

<!-- Check browser time
     - First request: slower (API call)
     - Second request: instant (from cache)
-->
```

#### Test 3: Verify Fallback
```blade
<!-- Disconnect internet or block translate.googleapis.com -->
@t('This text should show original if API fails')
```

### Performance Tips

1. **Short text translates faster**
   - ✅ Good: `@t('Simpan')`
   - ❌ Bad: `@t('Ini adalah daftar panjang dari semua fitur yang tersedia di aplikasi kami dengan penjelasan detail')`

2. **Use cache effectively**
   - Repeated translations are instant
   - First 30 days will hit cache 100%

3. **Avoid dynamic content in @t()**
   - ✅ Good: `@t('Halo') . ' ' . $username`
   - ❌ Bad: `@t('Halo ' . $username)`

4. **Batch similar translations**
   - Group related text together
   - Easier to review and maintain

### Files to Know

| File | Purpose |
|------|---------|
| `app/Services/GoogleTranslateService.php` | Translation engine |
| `app/Providers/AppServiceProvider.php` | Register @t() directive |
| `app/Http/Middleware/SetLocale.php` | Set locale per request |
| `resources/views/partials/language-switcher.blade.php` | Language dropdown |
| `config/app.php` | Default locale config |
| `TRANSLATION_GUIDE.md` | Full documentation |
| `TRANSLATION_QUICK_START.md` | Developer guide |

### Debugging

#### Enable Debug Mode
Add to `.env`:
```
APP_DEBUG=true
```

#### Check Locale in Template
```blade
<!-- Shows current locale -->
{{ app()->getLocale() }}

<!-- Conditional logic -->
@if(app()->getLocale() === 'en')
  <p>You are viewing in English</p>
@else
  <p>Anda sedang melihat dalam Bahasa Indonesia</p>
@endif
```

#### Check Service Directly
```php
// In a controller
$service = app(\App\Services\GoogleTranslateService::class);
$text = $service->translate('Selamat datang', 'en');
dd($text); // Should show: "Welcome"
```

#### Clear All Caches
```bash
php artisan cache:clear
php artisan view:clear
php artisan config:clear
```

---

## Checklist for Adding Translations

### For Each Page/Component:

- [ ] Identify all user-facing text
- [ ] Wrap text with `@t('Indonesian text')`
- [ ] Test with `?lang=en` parameter
- [ ] Verify English translation appears
- [ ] Test with `?lang=id` to switch back
- [ ] Check mobile/responsive view
- [ ] Review for special characters
- [ ] Commit changes to git

---

## Common Indonesian → English

| Indonesian | English |
|------------|---------|
| Selamat datang | Welcome |
| Simpan | Save |
| Hapus | Delete |
| Edit | Edit |
| Batal | Cancel |
| Kirim | Send |
| Cari | Search |
| Filter | Filter |
| Sortir | Sort |
| Halaman Beranda | Home Page |
| Resume Saya | My Resumes |
| Cari Pekerjaan | Find Jobs |
| Terapkan | Apply |
| Berhasil | Success |
| Kesalahan | Error |
| Peringatan | Warning |
| Tidak Ada Data | No Data |
| Muat Lebih Banyak | Load More |
| Perbarui | Update |
| Buat Baru | Create New |

---

## Support

- 📖 Read `TRANSLATION_GUIDE.md` for complete details
- 🚀 Check `TRANSLATION_QUICK_START.md` for quick examples
- 📝 See `TRANSLATION_IMPLEMENTATION_SUMMARY.md` for architecture
- 💬 Ask team for review before deploying

---

**Status:** ✅ Production Ready  
**Version:** 1.0  
**Last Updated:** January 2025
