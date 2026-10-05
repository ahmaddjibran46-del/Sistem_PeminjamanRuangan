@props(['class' => 'h-9 w-9'])
{{-- Logo PinjamRuang: dokumen peminjaman + centang persetujuan (sesuai desain Figma) --}}
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 36 36" role="img" aria-label="PinjamRuang" {{ $attributes->merge(['class' => $class.' shrink-0']) }}>
    <rect width="36" height="36" rx="9" fill="#057d6b"/>
    <rect x="8" y="9" width="20" height="17" rx="2.5" fill="#06604f"/>
    <rect x="11" y="12.5" width="14" height="2.2" rx="1.1" fill="#fff"/>
    <rect x="11" y="17" width="11" height="2.2" rx="1.1" fill="#fff"/>
    <rect x="11" y="21.5" width="7" height="2.2" rx="1.1" fill="#fff"/>
    <circle cx="26" cy="25.5" r="5.6" fill="#fbbf24" stroke="#057d6b" stroke-width="1.4"/>
    <path d="m23.4 25.6 1.8 1.8 3.3-3.5" fill="none" stroke="#7a3f06" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
</svg>
