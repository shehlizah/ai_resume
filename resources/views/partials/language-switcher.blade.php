<div class="dropdown d-inline-block language-switcher">
    <button class="btn btn-light btn-sm dropdown-toggle d-flex align-items-center" type="button" id="langDropdown" data-bs-toggle="dropdown" aria-expanded="false">
        @if(app()->getLocale() === 'id')
            <img src="https://flagcdn.com/id.svg" width="22" class="me-1" alt="Indonesian"> <span class="d-none d-md-inline">Bahasa</span>
        @else
            <img src="https://flagcdn.com/us.svg" width="22" class="me-1" alt="English"> <span class="d-none d-md-inline">Language</span>
        @endif
    </button>
    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="langDropdown">
        <li>
            <a class="dropdown-item d-flex align-items-center {{ app()->getLocale() === 'en' ? 'active' : '' }}" href="{{ route('language.switch', 'en') }}">
                <img src="https://flagcdn.com/us.svg" width="20" class="me-2"> English
            </a>
        </li>
        <li><hr class="dropdown-divider"></li>
        <li>
            <a class="dropdown-item d-flex align-items-center {{ app()->getLocale() === 'id' ? 'active' : '' }}" href="{{ route('language.switch', 'id') }}">
                <img src="https://flagcdn.com/id.svg" width="20" class="me-2"> Bahasa Indonesia
            </a>
        </li>
    </ul>
</div>
<style>
.language-switcher .dropdown-menu { min-width: 180px; }
.language-switcher .dropdown-item.active { background-color: #f0f0f0; }
@media (max-width: 767.98px) {
    .language-switcher .dropdown-toggle span { display: none !important; }
}
</style>
