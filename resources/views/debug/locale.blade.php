<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Debug - Locale & Translation</title>
    <style>
        body {
            font-family: sans-serif;
            max-width: 800px;
            margin: 40px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .box {
            background: white;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 20px;
            margin: 15px 0;
        }
        .test-link {
            display: inline-block;
            padding: 10px 20px;
            margin: 10px 5px 10px 0;
            background: #3498db;
            color: white;
            text-decoration: none;
            border-radius: 4px;
        }
        .test-link:hover {
            background: #2980b9;
        }
        .success { color: green; }
        .error { color: red; }
        .warning { color: orange; }
        pre {
            background: #f9f9f9;
            padding: 10px;
            border-radius: 4px;
            overflow-x: auto;
        }
        h1, h2 {
            color: #333;
        }
        .info {
            background: #e3f2fd;
            border-left: 4px solid #2196f3;
            padding: 15px;
            margin: 15px 0;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <h1>🔍 Debug: Locale & Translation System</h1>

    <div class="box">
        <h2>Current Status</h2>
        <p><strong>Current Locale:</strong> <code>{{ $currentLocale }}</code></p>
        <p><strong>Browser Accept-Language:</strong> <code>{{ $acceptLanguage ?? 'Not set' }}</code></p>
        <p><strong>Test Translation:</strong></p>
        <pre>
Input (Indonesian):  {{ $testText }}
Output (English):    {{ $translation }}
        </pre>
    </div>

    <div class="box">
        <h2>Test Language Switching</h2>
        <p>Click to test language switching:</p>
        <a href="{{ url()->current() }}?lang=id" class="test-link">🇮🇩 Switch to Indonesian (?lang=id)</a>
        <a href="{{ url()->current() }}?lang=en" class="test-link">🇺🇸 Switch to English (?lang=en)</a>
    </div>

    <div class="box">
        <h2>What to Check</h2>
        <ul>
            <li>✅ <strong>Locale Changes:</strong> Click English link above and verify "Current Locale" becomes "en"</li>
            <li>✅ <strong>Translation Works:</strong> Output should show English text when locale is "en"</li>
            <li>✅ <strong>Persistence:</strong> Reload page after switching - locale should persist</li>
            <li>✅ <strong>Auto-Translate:</strong> All text on this page should automatically translate when locale is "en"</li>
        </ul>
    </div>

    <div class="box">
        <h2>HTML Content to Auto-Translate</h2>
        <p>
            Selamat datang ke sistem debug kami. Ini adalah halaman untuk menguji sistem terjemahan otomatis.
            Jika Anda berada di mode bahasa Inggris, semua teks pada halaman ini harus secara otomatis diterjemahkan.
        </p>
        <p>
            Cobalah untuk beralih ke bahasa Inggris menggunakan tautan di atas, dan kemudian reload halaman.
            Anda harus melihat semua konten diterjemahkan tanpa perlu membungkus teks dengan directive khusus.
        </p>
    </div>

    <div class="info">
        <strong>💡 How It Works:</strong>
        <p>
            1. Click "Switch to English" link<br>
            2. SetLocale middleware sets locale to 'en'<br>
            3. AutoTranslateResponse middleware intercepts the response<br>
            4. All text nodes are translated to English automatically<br>
            5. Language preference is persisted in a cookie<br>
        </p>
    </div>

    <div class="box">
        <h2>Diagnostics</h2>
        <p><strong>Session Locale:</strong> <code>{{ session('locale') ?? 'Not set' }}</code></p>
        <p><strong>Cookie Locale:</strong> <code>{{ request()->cookie('locale') ?? 'Not set' }}</code></p>
        <p><strong>Query Param:</strong> <code>{{ request()->query('lang') ?? 'Not set' }}</code></p>
    </div>
</body>
</html>
