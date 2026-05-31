@php $user = auth()->user(); @endphp

@if ($user?->isModerator())
    <div class="bg-emerald-700 text-white text-xs px-4 py-1.5 flex items-center gap-2">
        <svg class="w-3.5 h-3.5 opacity-70" fill="none" stroke="currentColor"
             viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955
                     11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824
                     10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133
                     -2.052-.382-3.016z"/>
        </svg>
        <span class="opacity-70">Program Moderator —</span>
        <span class="font-semibold">
            {{ $user->program?->code }}: {{ $user->program?->name }}
        </span>
    </div>

@elseif ($user?->isProgramHead())
    <div class="bg-navy-800 text-white text-xs px-4 py-1.5 flex items-center gap-2">
        <svg class="w-3.5 h-3.5 opacity-60" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
        </svg>
        <span class="opacity-60">Program Head —</span>
        <span class="font-semibold">{{ $user->college?->code }}: {{ $user->college?->name }}</span>
    </div>
@elseif ($user?->isDean())
    <div class="bg-indigo-800 text-white text-xs px-4 py-1.5 flex items-center gap-2">
        <svg class="w-3.5 h-3.5 opacity-60" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/>
        </svg>
        <span class="opacity-60">Dean —</span>
        <span class="font-semibold">{{ $user->college?->code }}: {{ $user->college?->name }}</span>
    </div>
@elseif ($user?->isSao())
    <div class="bg-slate-700 text-white text-xs px-4 py-1.5 flex items-center gap-2">
        <svg class="w-3.5 h-3.5 opacity-60" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
        </svg>
        <span class="opacity-60">Safety & Security Office —</span>
        <span class="font-semibold">SEAIT Campus Administration</span>
    </div>
@elseif ($user?->isSuperAdmin())
    <div class="bg-gray-900 text-white text-xs px-4 py-1.5 flex items-center gap-2">
        <svg class="w-3.5 h-3.5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.054 0 1.502-1.275.722-1.845l-6.928-5.013c-.752-.545-1.792-.545-2.544 0L5.094 17.155c-.78.57-.332 1.845.722 1.845z"/>
        </svg>
        <span class="font-semibold text-red-400">⚠ SYSTEM ADMINISTRATION MODE</span>
    </div>
@endif