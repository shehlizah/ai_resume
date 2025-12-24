# Quick Translation Integration Checklist

## For Developers: Adding Translations to Any Page

### Step 1: Identify Text to Translate
Find all user-facing text in your Blade template that needs translation.

### Step 2: Wrap with @t() Directive
Replace hardcoded text with the translation directive.

### Step 3: Use Indonesian as Source Text
Always use **Indonesian** as the source language in the directive.

## Before & After Examples

### Example 1: Simple Text
```blade
<!-- BEFORE -->
<h1>Welcome to JobsEase</h1>

<!-- AFTER -->
<h1>@t('Selamat datang di JobsEase')</h1>
```

### Example 2: Button with Icon
```blade
<!-- BEFORE -->
<a href="{{ route('user.resumes.create') }}" class="btn btn-primary">
    <i class="bx bx-plus"></i> Create Resume
</a>

<!-- AFTER -->
<a href="{{ route('user.resumes.create') }}" class="btn btn-primary">
    <i class="bx bx-plus"></i> @t('Buat Resume')
</a>
```

### Example 3: Form Labels
```blade
<!-- BEFORE -->
<label class="form-label">Full Name</label>
<input type="text" class="form-control" />

<!-- AFTER -->
<label class="form-label">@t('Nama Lengkap')</label>
<input type="text" class="form-control" />
```

### Example 4: Placeholder Text
```blade
<!-- BEFORE -->
<input type="email" placeholder="Enter your email address" />

<!-- AFTER -->
<input type="email" placeholder="@t('Masukkan alamat email Anda')" />
```

### Example 5: Alert Messages
```blade
<!-- BEFORE -->
<div class="alert alert-success">
    Your resume has been saved successfully!
</div>

<!-- AFTER -->
<div class="alert alert-success">
    @t('Resume Anda telah disimpan dengan berhasil!')
</div>
```

### Example 6: Dropdown Options
```blade
<!-- BEFORE -->
<select class="form-select">
    <option value="">Select Status</option>
    <option value="active">Active</option>
    <option value="inactive">Inactive</option>
</select>

<!-- AFTER -->
<select class="form-select">
    <option value="">@t('Pilih Status')</option>
    <option value="active">@t('Aktif')</option>
    <option value="inactive">@t('Tidak Aktif')</option>
</select>
```

### Example 7: Table Headers
```blade
<!-- BEFORE -->
<thead>
    <tr>
        <th>Name</th>
        <th>Email</th>
        <th>Status</th>
        <th>Actions</th>
    </tr>
</thead>

<!-- AFTER -->
<thead>
    <tr>
        <th>@t('Nama')</th>
        <th>@t('Email')</th>
        <th>@t('Status')</th>
        <th>@t('Tindakan')</th>
    </tr>
</thead>
```

### Example 8: Conditional Messages
```blade
<!-- BEFORE -->
@if($results->isEmpty())
    <p>No results found. Try a different search.</p>
@endif

<!-- AFTER -->
@if($results->isEmpty())
    <p>@t('Tidak ada hasil ditemukan. Coba pencarian yang berbeda.')</p>
@endif
```

### Example 9: Pagination/Loading
```blade
<!-- BEFORE -->
<button class="btn btn-primary">
    Loading...
</button>

<!-- AFTER -->
<button class="btn btn-primary">
    @t('Memuat...')
</button>
```

### Example 10: Validation Errors
```blade
<!-- BEFORE -->
<span class="text-danger">This field is required</span>

<!-- AFTER -->
<span class="text-danger">@t('Bidang ini diperlukan')</span>
```

## Files Already Updated

The following files have been updated with translation support:

- ✅ `resources/views/admin/dashboard/index.blade.php` - Dashboard welcome & stats
- ✅ `resources/views/components/auth-header.blade.php` - Auth form headers
- ✅ `resources/views/user/resumes/index.blade.php` - Resume management page

## Files Recommended for Translation

Priority order for next updates:

1. **Frontend Pages** (highest priority - user-facing)
   - `resources/views/frontend/pages/home.blade.php`
   - `resources/views/frontend/pages/pricing.html`
   - `resources/views/frontend/pages/about.html`

2. **Auth Pages** (user signup/login)
   - Login form
   - Registration form
   - Password reset form

3. **User Dashboard Pages**
   - Jobs listing page
   - Cover letters page
   - Interview practice page
   - Subscription page

4. **Admin Pages** (internal tools)
   - User management
   - Template management
   - Payment management

## How Translation Works

1. **User is in Indonesian (default)**
   - Sees: "Selamat datang di JobsEase" (original text)
   - No API call made

2. **User switches to English (`?lang=en`)**
   - Sees: "Welcome to JobsEase" (translated text)
   - First time: API calls Google Translate
   - Subsequent times: Uses 30-day cache

3. **Switch back to Indonesian**
   - Sees: "Selamat datang di JobsEase" (original text again)

## Common Indonesian Phrases

| Indonesian | English |
|------------|---------|
| Selamat datang | Welcome |
| Halo | Hello |
| Terima kasih | Thank you |
| Tolong | Please |
| Ya | Yes |
| Tidak | No |
| Simpan | Save |
| Batal | Cancel |
| Hapus | Delete |
| Edit | Edit |
| Lihat | View |
| Buat | Create |
| Tambah | Add |
| Cari | Search |
| Filter | Filter |
| Sortir | Sort |
| Tidak ada data | No data |
| Berhasil | Success |
| Kesalahan | Error |
| Peringatan | Warning |

## Testing Your Changes

### Step 1: Visit Your Page
```
http://localhost:8000/your-page
```

### Step 2: Verify Indonesian Shows
- Should see Indonesian text (default)

### Step 3: Switch to English
```
http://localhost:8000/your-page?lang=en
```

### Step 4: Verify English Shows
- Should see English text (translated)
- Check browser console for any errors
- Check logs if translation fails

### Step 5: Switch Back to Indonesian
```
http://localhost:8000/your-page?lang=id
```

### Step 6: Verify Indonesian Shows Again
- Should switch back to original text

## Troubleshooting

### Translation Not Appearing
1. Check that text is wrapped in `@t('text')`
2. Clear browser cache (Ctrl+Shift+Delete)
3. Hard refresh page (Ctrl+F5)
4. Check that locale is 'en': `{{ app()->getLocale() }}`

### Text Translating But Looks Wrong
1. Translation is cached - clear cache: `php artisan cache:clear`
2. Check if it's a complex phrase - try shorter phrases
3. If text has special characters, wrap in quotes properly

### API Errors
1. Check internet connection
2. Check server logs: `tail -f storage/logs/laravel.log`
3. Verify firewall allows access to `translate.googleapis.com`

## Performance Tips

1. **Keep text short** - Fewer words translate faster
2. **Cache is your friend** - First request is slower, rest are instant
3. **Use simple language** - Easy Indonesian translates better
4. **Avoid special characters** - Plain text works best
5. **Test with real users** - Get feedback on translation quality

## Need Help?

1. Check `TRANSLATION_GUIDE.md` for detailed documentation
2. Review examples in `app/Services/GoogleTranslateService.php`
3. Check blade directives in `app/Providers/AppServiceProvider.php`
4. Look at updated files for patterns: `admin/dashboard/index.blade.php`

---

**Last Updated:** January 2025  
**Maintainer:** Development Team  
**Status:** ✅ Active and Tested
