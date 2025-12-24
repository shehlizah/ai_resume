@php
    $isEn = app()->getLocale() === 'en';
    $next = $isEn ? 'id' : 'en';
    $flag = $isEn ? 'https://flagcdn.com/id.svg' : 'https://flagcdn.com/us.svg';
@endphp
<a href="{{ route('language.switch', $next) }}"
   class="d-inline-flex align-items-center justify-content-center"
   style="width:44px;height:44px;border:1px solid #e0e0e0;border-radius:12px;padding:4px;background:#fff;">
    <img src="{{ $flag }}" width="22" height="22" alt="Switch language" style="object-fit:cover;">
</a>
