<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 316 316" fill="none" {{ $attributes }}>
  <defs>
    <linearGradient id="shGrad" x1="0%" y1="0%" x2="100%" y2="100%">
      <stop offset="0%" style="stop-color:#FF6B35"/>
      <stop offset="100%" style="stop-color:#E5512A"/>
    </linearGradient>
    <linearGradient id="shGradLight" x1="0%" y1="100%" x2="100%" y2="0%">
      <stop offset="0%" style="stop-color:#FF8C5A"/>
      <stop offset="100%" style="stop-color:#FF6B35"/>
    </linearGradient>
  </defs>
  <!-- Outer ring -->
  <circle cx="158" cy="158" r="138" stroke="url(#shGrad)" stroke-width="6" fill="none" opacity="0.15"/>
  <circle cx="158" cy="158" r="128" stroke="url(#shGrad)" stroke-width="2" fill="none" opacity="0.08"/>
  <!-- Main book shape -->
  <path d="M118 88h80c8 0 14 6 14 14v128c0 8-6 14-14 14h-80c-8 0-14-6-14-14V102c0-8 6-14 14-14z" fill="url(#shGrad)" opacity="0.95"/>
  <!-- Book spine -->
  <path d="M118 88v156" stroke="white" stroke-width="2" opacity="0.3"/>
  <!-- Left page -->
  <path d="M118 102h40v128h-40z" fill="white" opacity="0.12"/>
  <!-- Page lines left -->
  <line x1="128" y1="118" x2="148" y2="118" stroke="white" stroke-width="2" opacity="0.4" stroke-linecap="round"/>
  <line x1="128" y1="130" x2="148" y2="130" stroke="white" stroke-width="2" opacity="0.4" stroke-linecap="round"/>
  <line x1="128" y1="142" x2="148" y2="142" stroke="white" stroke-width="2" opacity="0.4" stroke-linecap="round"/>
  <line x1="128" y1="154" x2="140" y2="154" stroke="white" stroke-width="2" opacity="0.3" stroke-linecap="round"/>
  <!-- Right page lines -->
  <line x1="168" y1="118" x2="188" y2="118" stroke="white" stroke-width="2" opacity="0.4" stroke-linecap="round"/>
  <line x1="168" y1="130" x2="188" y2="130" stroke="white" stroke-width="2" opacity="0.4" stroke-linecap="round"/>
  <line x1="168" y1="142" x2="188" y2="142" stroke="white" stroke-width="2" opacity="0.4" stroke-linecap="round"/>
  <line x1="168" y1="154" x2="180" y2="154" stroke="white" stroke-width="2" opacity="0.3" stroke-linecap="round"/>
  <!-- Bookmark ribbon -->
  <path d="M158 88l-8 20h16z" fill="#FF8C5A"/>
  <path d="M158 108v20" stroke="#FF8C5A" stroke-width="3" stroke-linecap="round"/>
  <!-- "SH" monogram on book cover -->
  <text x="158" y="200" text-anchor="middle" fill="white" font-family="Figtree, sans-serif" font-weight="800" font-size="36" letter-spacing="2">SH</text>
  <!-- Decorative dots around ring -->
  <circle cx="158" cy="26" r="5" fill="url(#shGradLight)"/>
  <circle cx="270" cy="90" r="5" fill="url(#shGradLight)"/>
  <circle cx="290" cy="158" r="5" fill="url(#shGradLight)"/>
  <circle cx="270" cy="226" r="5" fill="url(#shGradLight)"/>
  <circle cx="158" cy="290" r="5" fill="url(#shGradLight)"/>
  <circle cx="46" cy="226" r="5" fill="url(#shGradLight)"/>
  <circle cx="26" cy="158" r="5" fill="url(#shGradLight)"/>
  <circle cx="46" cy="90" r="5" fill="url(#shGradLight)"/>
</svg>