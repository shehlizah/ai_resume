@php
    $currentLocale = app()->getLocale();
    $locales = [
        'en' => ['name' => 'English', 'flag' => '🇺🇸'],
        'id' => ['name' => 'Bahasa Indonesia', 'flag' => '🇮🇩'],
    ];
@endphp

<li class="nav-item language-switcher-item">
    <div class="language-switcher-wrapper">
        <button class="language-switcher-toggle" id="languageSwitcherToggle">
            <span class="language-label">{{ strtoupper($currentLocale) }}</span>
            <span class="language-caret">▼</span>
        </button>
        
        <div class="language-dropdown" id="languageDropdown">
            @foreach($locales as $locale => $data)
                <a href="{{ route('language.switch', $locale) }}" 
                   class="language-item {{ $locale === $currentLocale ? 'active' : '' }}"
                   data-locale="{{ $locale }}">
                    <span class="language-flag">{{ $data['flag'] }}</span>
                    <span class="language-text">{{ $data['name'] }}</span>
                    @if($locale === $currentLocale)
                        <span class="language-check">✓</span>
                    @endif
                </a>
            @endforeach
        </div>
    </div>
</li>

<style>
    .language-switcher-item {
        position: relative;
        margin: 0 8px;
    }

    .language-switcher-wrapper {
        position: relative;
    }

    .language-switcher-toggle {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 0;
        background: none;
        border: none;
        color: currentColor;
        cursor: pointer;
        font-size: 14px;
        font-weight: 500;
        font-family: inherit;
        line-height: 1.5;
        transition: color 0.2s ease;
    }

    .language-switcher-toggle:hover {
        color: #667eea;
        text-decoration: underline;
    }

    .language-label {
        display: inline-block;
    }

    .language-caret {
        display: inline-block;
        font-size: 10px;
        transition: transform 0.2s ease;
    }

    .language-switcher-toggle.active .language-caret {
        transform: rotate(-180deg);
    }

    .language-dropdown {
        position: absolute;
        top: calc(100% + 8px);
        right: 0;
        background: white;
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        min-width: 200px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        opacity: 0;
        visibility: hidden;
        transform: translateY(-8px);
        transition: all 0.2s ease;
        z-index: 1000;
    }

    .language-dropdown.show {
        opacity: 1;
        visibility: visible;
        transform: translateY(0);
    }

    .language-item {
        display: flex;
        align-items: center;
        padding: 12px 16px;
        color: #333;
        text-decoration: none;
        font-size: 14px;
        transition: background-color 0.2s ease;
        border-bottom: 1px solid #f0f0f0;
    }

    .language-item:last-child {
        border-bottom: none;
    }

    .language-item:hover {
        background-color: #f5f5f5;
    }

    .language-item.active {
        background-color: #f0f7ff;
        color: #667eea;
        font-weight: 600;
    }

    .language-flag {
        display: inline-block;
        font-size: 16px;
        margin-right: 10px;
        min-width: 20px;
        text-align: center;
    }

    .language-text {
        flex: 1;
    }

    .language-check {
        display: inline-block;
        color: #667eea;
        font-weight: bold;
        margin-left: auto;
    }

    /* Mobile responsiveness */
    @media (max-width: 768px) {
        .language-switcher-item {
            margin: 0 4px;
        }

        .language-dropdown {
            right: -50px;
        }

        .language-switcher-toggle {
            font-size: 13px;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const toggle = document.getElementById('languageSwitcherToggle');
        const dropdown = document.getElementById('languageDropdown');

        if (!toggle || !dropdown) return;

        // Toggle dropdown on click
        toggle.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            dropdown.classList.toggle('show');
            toggle.classList.toggle('active');
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!toggle.contains(e.target) && !dropdown.contains(e.target)) {
                dropdown.classList.remove('show');
                toggle.classList.remove('active');
            }
        });

        // Close dropdown when a language is selected
        const items = dropdown.querySelectorAll('.language-item');
        items.forEach(item => {
            item.addEventListener('click', function() {
                // Save language preference to localStorage
                const locale = this.dataset.locale;
                localStorage.setItem('preferred_language', locale);
                // Navigation happens via href
            });
        });

        // Auto-detect language on first visit
        if (!localStorage.getItem('preferred_language')) {
            const browserLang = navigator.language || navigator.userLanguage;
            if (browserLang.startsWith('id')) {
                localStorage.setItem('preferred_language', 'id');
            } else {
                localStorage.setItem('preferred_language', 'en');
            }
        }
    });
</script>
