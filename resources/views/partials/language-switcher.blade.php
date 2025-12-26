@php
    $currentLocale = app()->getLocale();
    $locales = [
        'en' => ['name' => 'English', 'flag' => '🇺🇸', 'code' => 'EN'],
        'id' => ['name' => 'Bahasa Indonesia', 'flag' => '🇮🇩', 'code' => 'ID'],
    ];
    $current = $locales[$currentLocale] ?? $locales['en'];
@endphp

<li class="nav-item language-switcher-item">
    <div class="language-switcher-wrapper">
        <button class="language-switcher-toggle" id="languageSwitcherToggle">
            <span class="language-flag">{{ $current['flag'] }}</span>
            <span class="language-code">{{ $current['code'] }}</span>
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
        display: flex;
        align-items: center;
        margin: 0 12px;
        list-style: none;
    }

    .language-switcher-wrapper {
        position: relative;
    }

    .language-switcher-toggle {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 0;
        background: none;
        border: none;
        color: currentColor;
        cursor: pointer;
        font-size: 14px;
        font-weight: 500;
        font-family: inherit;
        line-height: 1.5;
        height: auto;
        white-space: nowrap;
        vertical-align: middle;
        transition: color 0.2s ease;
        margin: 0;
    }

    .language-switcher-toggle:hover {
        color: #667eea;
    }

    .language-flag {
        display: inline-block;
        font-size: 16px;
        min-width: 16px;
        text-align: center;
        line-height: 1.5;
    }

    .language-code {
        display: inline-block;
        font-weight: 600;
        line-height: 1.5;
    }

    .language-caret {
        display: inline-block;
        font-size: 10px;
        transition: transform 0.2s ease;
        margin-left: 5px;
        line-height: 1.5;
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
        min-width: 180px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        opacity: 0;
        visibility: hidden;
        transform: translateY(-8px);
        transition: all 0.2s ease;
        z-index: 1000;
        overflow: hidden;
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

    .language-item .language-flag {
        font-size: 18px;
        margin-right: 12px;
        min-width: 24px;
        text-align: center;
    }

    .language-item .language-text {
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
            margin: 0 6px;
        }

        .language-dropdown {
            right: -40px;
            min-width: 200px;
        }

        .language-switcher-toggle {
            font-size: 13px;
        }

        .language-flag {
            font-size: 14px;
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
