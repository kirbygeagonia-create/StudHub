<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 316 316" fill="none" {{ $attributes }}>
  <defs>
    <linearGradient id="shGrad" x1="0%" y1="0%" x2="100%" y2="100%">
      <stop offset="0%" style="stop-color:#FF6B35"/>
      <stop offset="100%" style="stop-color:#E5512A"/>
    </linearGradient>
  </defs>
  <!-- Outer hexagon -->
  <path d="M158 20 L287 94 L287 222 L158 296 L29 222 L29 94 Z" stroke="url(#shGrad)" stroke-width="8" fill="none" opacity="0.12"/>
  <!-- Inner hub hexagon -->
  <path d="M158 85 L217 119 L217 178 L158 212 L99 178 L99 119 Z" fill="url(#shGrad)" opacity="0.9"/>
  <!-- Center dot -->
  <circle cx="158" cy="148" r="12" fill="white"/>
  <!-- Connecting lines from center to vertices -->
  <line x1="158" y1="148" x2="158" y2="85" stroke="url(#shGrad)" stroke-width="4" opacity="0.6"/>
  <line x1="158" y1="148" x2="217" y2="119" stroke="url(#shGrad)" stroke-width="4" opacity="0.6"/>
  <line x1="158" y1="148" x2="217" y2="178" stroke="url(#shGrad)" stroke-width="4" opacity="0.6"/>
  <line x1="158" y1="148" x2="158" y2="212" stroke="url(#shGrad)" stroke-width="4" opacity="0.6"/>
  <line x1="158" y1="148" x2="99" y2="178" stroke="url(#shGrad)" stroke-width="4" opacity="0.6"/>
  <line x1="158" y1="148" x2="99" y2="119" stroke="url(#shGrad)" stroke-width="4" opacity="0.6"/>
  <!-- Small program nodes on outer hexagon -->
  <circle cx="158" cy="40" r="6" fill="#FF6B35"/>
  <circle cx="277" cy="108" r="6" fill="#FF6B35"/>
  <circle cx="277" cy="210" r="6" fill="#FF6B35"/>
  <circle cx="158" cy="278" r="6" fill="#FF6B35"/>
  <circle cx="39" cy="210" r="6" fill="#FF6B35"/>
  <circle cx="39" cy="108" r="6" fill="#FF6B35"/>
</svg>