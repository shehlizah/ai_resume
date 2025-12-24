<div class="dropdown d-inline-block language-switcher">
    <button class="btn btn-sm dropdown-toggle d-flex align-items-center gap-1" type="button" id="langDropdown" data-bs-toggle="dropdown" aria-expanded="false" style="padding: 0.35rem 0.75rem; background: transparent; border: 1px solid #e0e0e0;">
        @if(app()->getLocale() === 'id')
            <img src="https://flagcdn.com/id.svg" width="18" alt="Indonesian"> <span class="d-none d-md-inline" style="font-size: 0.85rem;">Bahasa</span>
        @else
            <img src="https://flagcdn.com/us.svg" width="18" alt="English"> <span class="d-none d-md-inline" style="font-size: 0.85rem;">English</span>
        @endif
    </button>
    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="langDropdown" style="min-width: 180px;">
        <li>
            <a class="dropdown-item d-flex align-items-center {{ app()->getLocale() === 'en' ? 'active' : '' }}" href="{{ route('language.switch', 'en') }}" style="padding: 0.5rem 1rem;">
                <img src="https://flagcdn.com/us.svg" width="18" alt="English" class="me-2"> English
            </a>
        </li>
        <li><hr class="dropdown-divider m-0"></li>
        <li>
            <a class="dropdown-item d-flex align-items-center {{ app()->getLocale() === 'id' ? 'active' : '' }}" href="{{ route('language.switch', 'id') }}" style="padding: 0.5rem 1rem;">
                <img src="https://flagcdn.com/id.svg" width="18" alt="Indonesian" class="me-2"> Bahasa Indonesia
            </a>
        </li>
    </ul>
</div>
<style>
.language-switcher .dropdown-menu { 
    min-width: 180px; 
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}
.language-switcher .dropdown-item.active { 
    background-color: #f5f5f5; 
    color: #2563EB;
}
.language-switcher .dropdown-item:hover {
    background-color: #f9fafb;
}
@media (max-width: 576px) {
    .language-switcher .dropdown-toggle span { 
        display: none !important; 
    }
    .language-switcher .dropdown-toggle {
        padding: 0.35rem 0.5rem !important;
    }
}
</style>
