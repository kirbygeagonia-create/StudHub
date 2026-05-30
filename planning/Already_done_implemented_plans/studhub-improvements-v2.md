# StudHub UI/UX Improvements — Full File Replacements
> Every section below is a **complete file replacement**. Copy the entire code block and overwrite the file at the given path. Do not run npm, vite, or artisan commands — only file writes.

---

## FILE 1 — `resources/views/components/dropdown-link.blade.php`
**Problem:** Icons appear above/misaligned with text because `block` has no flex alignment.

```blade
<a {{ $attributes->merge(['class' => 'flex items-center w-full px-3 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-navy-700/60 focus:outline-none transition duration-150 ease-in-out rounded-lg mx-1 my-0.5']) }}>{{ $slot }}</a>
```

---

## FILE 2 — `resources/views/components/application-logo.blade.php`
**Problem:** Logo is a generic book-with-dots that feels robotic. Replace with an organic graduation cap + open book mark.

```blade
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 200 200" fill="none" {{ $attributes }}>
  <defs>
    <linearGradient id="shPrimary" x1="0" y1="0" x2="1" y2="1">
      <stop offset="0%" stop-color="#FF6B35"/>
      <stop offset="100%" stop-color="#C94B15"/>
    </linearGradient>
    <linearGradient id="shAccent" x1="0" y1="1" x2="1" y2="0">
      <stop offset="0%" stop-color="#FFB347"/>
      <stop offset="100%" stop-color="#FF8C5A"/>
    </linearGradient>
  </defs>
  <!-- Organic rounded base -->
  <path d="M100 18 C68 18 38 30 24 52 C10 74 10 100 10 100 C10 132 12 152 30 166 C48 180 72 182 100 182 C128 182 152 180 170 166 C188 152 190 132 190 100 C190 100 190 74 176 52 C162 30 132 18 100 18Z" fill="url(#shPrimary)"/>
  <!-- Open book left page -->
  <path d="M100 52 C88 54 66 62 54 74 L54 148 C66 138 88 132 100 132 Z" fill="white" opacity="0.16"/>
  <!-- Open book right page -->
  <path d="M100 52 C112 54 134 62 146 74 L146 148 C134 138 112 132 100 132 Z" fill="white" opacity="0.10"/>
  <!-- Spine -->
  <line x1="100" y1="52" x2="100" y2="148" stroke="white" stroke-width="2" opacity="0.28" stroke-linecap="round"/>
  <!-- Graduation cap board -->
  <rect x="72" y="94" width="56" height="8" rx="4" fill="white" opacity="0.95"/>
  <!-- Cap diamond top -->
  <path d="M100 70 L118 91 L100 99 L82 91 Z" fill="white" opacity="0.95"/>
  <!-- Tassel -->
  <line x1="116" y1="97" x2="116" y2="122" stroke="url(#shAccent)" stroke-width="3" stroke-linecap="round"/>
  <circle cx="116" cy="126" r="4.5" fill="url(#shAccent)"/>
  <!-- Left page lines -->
  <line x1="62" y1="114" x2="88" y2="111" stroke="white" stroke-width="2" stroke-linecap="round" opacity="0.35"/>
  <line x1="62" y1="124" x2="88" y2="121" stroke="white" stroke-width="2" stroke-linecap="round" opacity="0.25"/>
  <line x1="62" y1="134" x2="80" y2="131" stroke="white" stroke-width="2" stroke-linecap="round" opacity="0.18"/>
</svg>
```

---

## FILE 3 — `public/favicon.svg`
**Problem:** Current favicon is a plain purple rectangle with text.

```svg
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100">
  <defs>
    <linearGradient id="fg" x1="0" y1="0" x2="1" y2="1">
      <stop offset="0%" stop-color="#FF6B35"/>
      <stop offset="100%" stop-color="#C94B15"/>
    </linearGradient>
    <linearGradient id="fa" x1="0" y1="1" x2="1" y2="0">
      <stop offset="0%" stop-color="#FFB347"/>
      <stop offset="100%" stop-color="#FF8C5A"/>
    </linearGradient>
  </defs>
  <path d="M50 5 C32 5 14 13 7 27 C0 41 0 50 0 50 C0 63 1 73 11 81 C21 89 34 93 50 93 C66 93 79 89 89 81 C99 73 100 63 100 50 C100 50 100 41 93 27 C86 13 68 5 50 5Z" fill="url(#fg)"/>
  <path d="M50 22 C43 23 30 28 24 36 L24 74 C30 68 43 64 50 64 Z" fill="white" opacity="0.16"/>
  <path d="M50 22 C57 23 70 28 76 36 L76 74 C70 68 57 64 50 64 Z" fill="white" opacity="0.10"/>
  <line x1="50" y1="22" x2="50" y2="68" stroke="white" stroke-width="1.5" opacity="0.25"/>
  <rect x="36" y="45" width="28" height="6" rx="3" fill="white" opacity="0.95"/>
  <path d="M50 32 L60 44 L50 49 L40 44 Z" fill="white" opacity="0.95"/>
  <line x1="59" y1="47" x2="59" y2="62" stroke="url(#fa)" stroke-width="2.5" stroke-linecap="round"/>
  <circle cx="59" cy="65" r="3.5" fill="url(#fa)"/>
</svg>
```

---

## FILE 4 — `resources/views/layouts/navigation.blade.php`
**Problems fixed:**
- Logo inline SVG updated to match new design
- Logout now has a confirmation modal (no more accidental logout)
- Notification bell now polls every 60 seconds instead of only on page load
- Mobile logout also uses the confirmation modal

```blade
<nav x-data="{ open: false, scrolled: false, logoutOpen: false }" @scroll.window="scrolled = window.scrollY > 10" :class="{'shadow-md': scrolled}" class="sticky top-0 z-40 bg-white/95 dark:bg-navy-900/95 backdrop-blur-xl border-b border-gray-200/50 dark:border-navy-700/30" role="navigation" aria-label="Main navigation">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center gap-1">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5 mr-6 group">
                    <div class="w-8 h-8 flex items-center justify-center flex-shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 200 200" fill="none" class="w-8 h-8">
                            <defs>
                                <linearGradient id="shNavG" x1="0" y1="0" x2="1" y2="1">
                                    <stop offset="0%" stop-color="#FF6B35"/>
                                    <stop offset="100%" stop-color="#C94B15"/>
                                </linearGradient>
                                <linearGradient id="shNavA" x1="0" y1="1" x2="1" y2="0">
                                    <stop offset="0%" stop-color="#FFB347"/>
                                    <stop offset="100%" stop-color="#FF8C5A"/>
                                </linearGradient>
                            </defs>
                            <path d="M100 18 C68 18 38 30 24 52 C10 74 10 100 10 100 C10 132 12 152 30 166 C48 180 72 182 100 182 C128 182 152 180 170 166 C188 152 190 132 190 100 C190 100 190 74 176 52 C162 30 132 18 100 18Z" fill="url(#shNavG)"/>
                            <path d="M100 52 C88 54 66 62 54 74 L54 148 C66 138 88 132 100 132 Z" fill="white" opacity="0.16"/>
                            <path d="M100 52 C112 54 134 62 146 74 L146 148 C134 138 112 132 100 132 Z" fill="white" opacity="0.10"/>
                            <line x1="100" y1="52" x2="100" y2="148" stroke="white" stroke-width="2" opacity="0.28" stroke-linecap="round"/>
                            <rect x="72" y="94" width="56" height="8" rx="4" fill="white" opacity="0.95"/>
                            <path d="M100 70 L118 91 L100 99 L82 91 Z" fill="white" opacity="0.95"/>
                            <line x1="116" y1="97" x2="116" y2="122" stroke="url(#shNavA)" stroke-width="3" stroke-linecap="round"/>
                            <circle cx="116" cy="126" r="4.5" fill="url(#shNavA)"/>
                        </svg>
                    </div>
                    <span class="text-lg font-bold text-gray-900 tracking-tight dark:text-gray-100 group-hover:text-seait-500 dark:group-hover:text-seait-400 transition-colors duration-200">StudHub</span>
                </a>

                <div class="hidden sm:flex sm:items-center sm:gap-0.5">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        <x-icon name="home" class="w-4 h-4 mr-1.5" />
                        Home
                    </x-nav-link>
                    <x-nav-link :href="route('chat.index')" :active="request()->routeIs('chat.*')">
                        <x-icon name="chat" class="w-4 h-4 mr-1.5" />
                        Chat
                    </x-nav-link>
                    <x-nav-link :href="route('resources.index')" :active="request()->routeIs('resources.*')">
                        <x-icon name="resources" class="w-4 h-4 mr-1.5" />
                        Resources
                    </x-nav-link>
                    <x-nav-link :href="route('leaderboard')" :active="request()->routeIs('leaderboard')">
                        <x-icon name="leaderboard" class="w-4 h-4 mr-1.5" />
                        Leaderboard
                    </x-nav-link>
                    <x-nav-link :href="route('lends.index')" :active="request()->routeIs('lends.*')">
                        <x-icon name="lends" class="w-4 h-4 mr-1.5" />
                        Lends
                    </x-nav-link>
                    @if (Auth::user()?->isModerator() || Auth::user()?->isAdmin())
                        <x-nav-link :href="route('moderation.dashboard')" :active="request()->routeIs('moderation.*')">
                            <x-icon name="moderation" class="w-4 h-4 mr-1.5" />
                            Moderation
                        </x-nav-link>
                    @endif
                    @if (Auth::user()?->isAdmin())
                        <x-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.*')">
                            <x-icon name="admin" class="w-4 h-4 mr-1.5" />
                            Admin
                        </x-nav-link>
                    @endif
                </div>
            </div>

            <div class="hidden sm:flex sm:items-center sm:gap-2">
                <form method="GET" action="{{ route('search') }}" role="search" class="relative">
                    <label for="nav-search" class="sr-only">Search resources</label>
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <x-icon name="search" class="h-4 w-4 text-gray-400" />
                    </div>
                    <input type="text" id="nav-search" name="q" value="{{ request('q') }}"
                           placeholder="Search resources…"
                           class="w-48 pl-10 pr-3 py-2 text-sm rounded-xl border-gray-200 bg-gray-50/80 focus:bg-white focus:border-seait-400 focus:ring-2 focus:ring-seait-100 transition-all placeholder:text-gray-400 dark:bg-navy-800/60 dark:border-navy-700/50 dark:text-gray-200 dark:placeholder:text-gray-500 dark:focus:border-seait-500 dark:focus:ring-seait-800/30">
                </form>

                <!-- Notifications Bell -->
                <div class="relative" x-data="{
                    notifOpen: false,
                    notifications: [],
                    unreadCount: 0,
                    init() {
                        this.fetchNotifications();
                        setInterval(() => { this.fetchNotifications(); }, 60000);
                    },
                    fetchNotifications() {
                        fetch('{{ route('notifications.fetch') }}', {
                            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
                            credentials: 'same-origin',
                        })
                        .then(r => r.ok ? r.json() : Promise.reject(r.status))
                        .then(data => {
                            this.notifications = data.notifications ?? [];
                            this.unreadCount = data.unread_count ?? 0;
                        })
                        .catch(() => {});
                    },
                    markAsRead(id, link) {
                        fetch('{{ url('/notifications') }}/' + id + '/read', {
                            method: 'POST',
                            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'X-Requested-With': 'XMLHttpRequest' }
                        })
                        .then(() => {
                            this.notifications = this.notifications.map(n => n.id === id ? { ...n, read: true } : n);
                            this.unreadCount = Math.max(0, this.unreadCount - 1);
                        })
                        .catch(() => {});
                        if (link) window.location.href = link;
                    }
                }">
                    <button @click="notifOpen = !notifOpen" @click.outside="notifOpen = false" class="relative p-2 rounded-xl hover:bg-gray-50/80 dark:hover:bg-navy-800/60 transition-all duration-200" aria-label="Notifications">
                        <x-icon name="notifications" class="w-5 h-5 text-gray-600 dark:text-gray-300" />
                        <span x-show="unreadCount > 0" class="absolute top-1 right-1 w-4 h-4 bg-red-500 text-white text-[10px] font-bold rounded-full flex items-center justify-center ring-2 ring-white dark:ring-navy-900" x-text="unreadCount > 9 ? '9+' : unreadCount"></span>
                    </button>
                    <div x-show="notifOpen"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 scale-100"
                         x-transition:leave-end="opacity-0 scale-95"
                         class="absolute right-0 mt-2 w-80 bg-white dark:bg-navy-800 rounded-2xl shadow-xl border border-gray-100 dark:border-navy-700/50 z-50 overflow-hidden"
                         x-cloak>
                        <div class="px-4 py-3 border-b border-gray-100 dark:border-navy-700/50 flex items-center justify-between">
                            <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Notifications</h3>
                            <span class="text-xs text-gray-500 dark:text-gray-400" x-text="unreadCount + ' unread'"></span>
                        </div>
                        <div class="max-h-72 overflow-y-auto">
                            <template x-if="notifications.length === 0">
                                <div class="px-4 py-8 text-center">
                                    <svg class="mx-auto h-8 w-8 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-2">No notifications yet</p>
                                </div>
                            </template>
                            <template x-for="notif in notifications" :key="notif.id">
                                <div @click="markAsRead(notif.id, notif.link)"
                                     class="flex items-start gap-3 px-4 py-3 hover:bg-gray-50 dark:hover:bg-navy-700/50 transition-colors border-b border-gray-50 dark:border-navy-700/30 last:border-b-0 cursor-pointer">
                                    <div :class="`w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0 ${notif.color === 'amber' ? 'bg-amber-100 text-amber-600 dark:bg-amber-900/30 dark:text-amber-300' : notif.color === 'emerald' ? 'bg-emerald-100 text-emerald-600 dark:bg-emerald-900/30 dark:text-emerald-300' : notif.color === 'blue' ? 'bg-blue-100 text-blue-600 dark:bg-blue-900/30 dark:text-blue-300' : notif.color === 'purple' ? 'bg-purple-100 text-purple-600 dark:bg-purple-900/30 dark:text-purple-300' : 'bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400'}`">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                                        </svg>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900 dark:text-gray-100" x-text="notif.title"></p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5" x-text="notif.time"></p>
                                    </div>
                                    <div x-show="!notif.read" class="w-2 h-2 bg-seait-500 rounded-full flex-shrink-0 mt-1.5"></div>
                                </div>
                            </template>
                        </div>
                        <div class="px-4 py-2.5 border-t border-gray-100 dark:border-navy-700/50 bg-gray-50/50 dark:bg-navy-800/50 flex items-center justify-between">
                            <a href="{{ route('notifications.index') }}" class="text-xs font-medium text-seait-600 hover:text-seait-700 dark:text-seait-400 dark:hover:text-seait-300 transition-colors">View all</a>
                            <a href="{{ route('profile.notification-preferences') }}" class="text-xs text-gray-400 hover:text-gray-600 dark:text-gray-500 dark:hover:text-gray-300 transition-colors">Settings</a>
                        </div>
                    </div>
                </div>

                <x-dropdown align="right" width="64">
                    <x-slot name="trigger">
                        <button class="flex items-center gap-2 px-3 py-2 rounded-xl hover:bg-gray-50/80 dark:hover:bg-navy-800/60 transition-all duration-200">
                            <div class="w-8 h-8 rounded-full bg-gradient-to-br from-seait-400 to-seait-600 flex items-center justify-center text-white text-xs font-bold ring-2 ring-seait-100 dark:ring-seait-800/40 shadow-sm">
                                {{ strtoupper(substr(Auth::user()?->preferredDisplayName() ?? '?', 0, 2)) }}
                            </div>
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300 hidden lg:block">{{ Auth::user()?->preferredDisplayName() ?? 'Guest' }}</span>
                            <x-icon name="chevron-down" class="h-4 w-4 text-gray-400" />
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <div class="px-3 py-2.5 border-b border-gray-100 dark:border-navy-700/50">
                            <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ Auth::user()?->name }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ Auth::user()?->email }}</p>
                        </div>
                        <div class="py-1">
                            <x-dropdown-link :href="route('profile.show')">
                                <x-icon name="profile" class="w-4 h-4 mr-2 flex-shrink-0" />
                                Profile
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('resources.shelf')">
                                <x-icon name="shelf" class="w-4 h-4 mr-2 flex-shrink-0" />
                                My Shelf
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('notifications.index')">
                                <x-icon name="notifications" class="w-4 h-4 mr-2 flex-shrink-0" />
                                Notifications
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('profile.notification-preferences')">
                                <x-icon name="settings" class="w-4 h-4 mr-2 flex-shrink-0" />
                                Notif. Settings
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('feedback.create')">
                                <x-icon name="feedback" class="w-4 h-4 mr-2 flex-shrink-0" />
                                Feedback
                            </x-dropdown-link>
                        </div>

                        <div class="border-t border-gray-100 dark:border-navy-700/50 py-1">
                            <button @click="dark = !dark" type="button"
                                    class="flex items-center w-full px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-navy-700/60 transition-colors rounded-lg mx-1 my-0.5">
                                <template x-if="!dark">
                                    <x-icon name="dark" class="w-4 h-4 mr-2 flex-shrink-0" />
                                </template>
                                <template x-if="dark">
                                    <x-icon name="light" class="w-4 h-4 mr-2 flex-shrink-0" />
                                </template>
                                <span x-text="dark ? 'Light mode' : 'Dark mode'"></span>
                            </button>
                        </div>

                        <div class="border-t border-gray-100 dark:border-navy-700/50 py-1">
                            <button type="button"
                                    @click="logoutOpen = true"
                                    class="flex items-center w-full px-3 py-2 text-sm text-red-600 hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-900/20 transition-colors rounded-lg mx-1 my-0.5">
                                <x-icon name="logout" class="w-4 h-4 mr-2 flex-shrink-0" />
                                Log Out
                            </button>
                            <form method="POST" action="{{ route('logout') }}" id="logout-form" class="hidden">
                                @csrf
                            </form>
                        </div>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Mobile hamburger -->
            <div class="-me-2 flex items-center sm:hidden relative z-10">
                <button @click="open = ! open" :aria-expanded="open" aria-controls="mobile-menu"
                        class="inline-flex items-center justify-center p-2 rounded-xl text-gray-500 hover:text-gray-700 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-seait-400 transition dark:text-gray-400 dark:hover:text-gray-200 dark:hover:bg-navy-800">
                    <svg class="h-5 w-5" :class="{'hidden': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    <svg class="h-5 w-5" :class="{'hidden': !open}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile menu -->
    <div id="mobile-menu" :class="{'block': open, 'hidden': !open}" class="hidden sm:hidden relative" x-cloak>
        <div class="bg-white/95 dark:bg-navy-900/95 backdrop-blur-xl border-b border-gray-200/50 dark:border-navy-700/30 shadow-lg">
            <div class="px-3 py-2 space-y-1">
                <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">Home</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('chat.index')" :active="request()->routeIs('chat.*')">Chat</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('resources.index')" :active="request()->routeIs('resources.*')">Resources</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('leaderboard')" :active="request()->routeIs('leaderboard')">Leaderboard</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('lends.index')" :active="request()->routeIs('lends.*')">Lends</x-responsive-nav-link>
                @if (Auth::user()?->isModerator() || Auth::user()?->isAdmin())
                    <x-responsive-nav-link :href="route('moderation.dashboard')" :active="request()->routeIs('moderation.*')">Moderation</x-responsive-nav-link>
                @endif
                @if (Auth::user()?->isAdmin())
                    <x-responsive-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.*')">Admin</x-responsive-nav-link>
                @endif
            </div>
            <div class="border-t border-gray-100 dark:border-navy-700/50 px-3 py-3 space-y-1">
                <div class="px-3 pb-2">
                    <p class="font-medium text-sm text-gray-900 dark:text-gray-100">{{ Auth::user()?->name ?? 'Guest' }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ Auth::user()?->email ?? '' }}</p>
                </div>
                <x-responsive-nav-link :href="route('profile.show')">Profile</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('resources.shelf')">My Shelf</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('notifications.index')">Notifications</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('profile.notification-preferences')">Notif. Settings</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('feedback.create')">Feedback</x-responsive-nav-link>
                <div class="px-3 py-2">
                    <button @click="dark = !dark" type="button" class="text-sm text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-200 flex items-center gap-2 transition-colors">
                        <template x-if="!dark">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
                        </template>
                        <template x-if="dark">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                        </template>
                        <span x-text="dark ? 'Switch to light mode' : 'Switch to dark mode'"></span>
                    </button>
                </div>
                <div class="px-3">
                    <button type="button"
                            @click="logoutOpen = true"
                            class="flex items-center gap-3 w-full px-3 py-2.5 rounded-lg text-sm font-medium text-red-600 hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-900/20 transition-colors">
                        <x-icon name="logout" class="w-4 h-4" />
                        Log Out
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Logout Confirmation Modal -->
    <div x-show="logoutOpen"
         x-cloak
         class="fixed inset-0 z-[9999] flex items-center justify-center p-4"
         @keydown.escape.window="logoutOpen = false">
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm"
             @click="logoutOpen = false"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"></div>
        <div class="relative bg-white dark:bg-navy-800 rounded-2xl shadow-2xl border border-gray-200 dark:border-navy-700/50 w-full max-w-sm p-6"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95 translate-y-2"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95 translate-y-2">
            <div class="w-12 h-12 rounded-2xl bg-red-100 dark:bg-red-900/30 flex items-center justify-center mx-auto mb-4">
                <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                </svg>
            </div>
            <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 text-center mb-1">Log out of StudHub?</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 text-center mb-6">You'll need to sign in again to access your resources, chats, and lends.</p>
            <div class="flex gap-3">
                <button type="button"
                        @click="logoutOpen = false"
                        class="flex-1 px-4 py-2.5 rounded-xl border border-gray-200 dark:border-navy-700 text-sm font-semibold text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-navy-700 transition-colors">
                    Stay logged in
                </button>
                <button type="button"
                        @click="document.getElementById('logout-form').submit()"
                        class="flex-1 px-4 py-2.5 rounded-xl bg-red-600 hover:bg-red-700 text-white text-sm font-semibold transition-colors shadow-sm">
                    Yes, log out
                </button>
            </div>
        </div>
    </div>
</nav>
```

---

## FILE 5 — `resources/views/layouts/app.blade.php`
**Problems fixed:**
- Validation error block (`$errors->any()`) now auto-dismisses after 8 seconds
- PWA install banner conflict fixed: Alpine's `x-show` removed, JS in app.js handles visibility directly via `hidden` class

```blade
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="{ dark: localStorage.getItem('dark') === 'true' }" x-init="if (dark) { document.documentElement.classList.add('dark') }; $watch('dark', v => { localStorage.setItem('dark', v); document.documentElement.classList.toggle('dark', v) })" :class="{ 'dark': dark }">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <link rel="icon" type="image/svg+xml" href="/favicon.svg">
        <meta name="theme-color" content="#FF6B35">
        <meta name="color-scheme" content="light dark">

        <title>@yield('title', config('app.name', 'Laravel'))</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

        <script>if (localStorage.getItem('dark') === 'true') document.documentElement.classList.add('dark')</script>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
    </head>
    <body class="font-sans antialiased text-gray-900 dark:text-gray-100">
        <div class="min-h-screen">
            @include('layouts.navigation')

            @isset($header)
                <header class="bg-white/60 backdrop-blur-sm border-b border-gray-100 dark:bg-navy-800/40 dark:border-navy-700/30">
                    <div class="max-w-7xl mx-auto py-3 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Flash Messages -->
            @if (session('status') || session('success'))
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
                    <div x-data="{ show: true }" x-show="show"
                         x-init="setTimeout(() => show = false, 5000)"
                         x-transition:leave="transition ease-in duration-300"
                         x-transition:leave-start="opacity-100 translate-y-0"
                         x-transition:leave-end="opacity-0 -translate-y-1"
                         class="flash-success">
                        <svg class="w-5 h-5 flex-shrink-0 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>{{ session('status') ?? session('success') }}</span>
                        <button @click="show = false" class="ml-auto text-emerald-500 hover:text-emerald-700 dark:hover:text-emerald-300">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                </div>
            @endif
            @if (session('error'))
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
                    <div x-data="{ show: true }" x-show="show"
                         x-init="setTimeout(() => show = false, 6000)"
                         x-transition:leave="transition ease-in duration-300"
                         x-transition:leave-start="opacity-100"
                         x-transition:leave-end="opacity-0"
                         class="flash-error">
                        <svg class="w-5 h-5 flex-shrink-0 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>{{ session('error') }}</span>
                        <button @click="show = false" class="ml-auto text-red-500 hover:text-red-700 dark:hover:text-red-300">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                </div>
            @endif
            @if (session('warning'))
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
                    <div x-data="{ show: true }" x-show="show"
                         x-init="setTimeout(() => show = false, 7000)"
                         x-transition:leave="transition ease-in duration-300"
                         x-transition:leave-start="opacity-100"
                         x-transition:leave-end="opacity-0"
                         class="flash-warning">
                        <svg class="w-5 h-5 flex-shrink-0 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.054 0 1.502-1.275.722-1.845l-6.928-5.013c-.752-.545-1.792-.545-2.544 0L5.094 17.155c-.78.57-.332 1.845.722 1.845z"/></svg>
                        <span>{{ session('warning') }}</span>
                        <button @click="show = false" class="ml-auto text-amber-500 hover:text-amber-700 dark:hover:text-amber-300">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                </div>
            @endif
            @if (session('info'))
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
                    <div x-data="{ show: true }" x-show="show"
                         x-init="setTimeout(() => show = false, 5000)"
                         x-transition:leave="transition ease-in duration-300"
                         x-transition:leave-start="opacity-100"
                         x-transition:leave-end="opacity-0"
                         class="flash-info">
                        <svg class="w-5 h-5 flex-shrink-0 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>{{ session('info') }}</span>
                        <button @click="show = false" class="ml-auto text-blue-500 hover:text-blue-700 dark:hover:text-blue-300">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                </div>
            @endif
            @if ($errors->any())
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
                    <div x-data="{ show: true }" x-show="show"
                         x-init="setTimeout(() => show = false, 8000)"
                         x-transition:leave="transition ease-in duration-300"
                         x-transition:leave-start="opacity-100"
                         x-transition:leave-end="opacity-0"
                         class="flash-error">
                        <svg class="w-5 h-5 flex-shrink-0 text-red-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <div>
                            <ul class="list-disc list-inside space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                        <button @click="show = false" class="ml-auto text-red-500 hover:text-red-700 dark:hover:text-red-300">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                </div>
            @endif

            <main>
                {{ $slot }}
            </main>
        </div>

        <!-- PWA Install Banner — visibility controlled by app.js via classList, NOT Alpine x-show -->
        <div id="pwa-install-banner"
             class="hidden fixed bottom-4 inset-x-4 z-50 card p-4 flex items-center justify-between gap-4 max-w-lg mx-auto shadow-card-lg">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-seait-100 dark:bg-seait-900/30 flex items-center justify-center flex-shrink-0">
                    <x-application-logo class="w-7 h-7" />
                </div>
                <div>
                    <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">Install StudHub</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Get faster access on your device</p>
                </div>
            </div>
            <div class="flex items-center gap-2 flex-shrink-0">
                <button onclick="installPwa()" class="btn-primary text-xs !px-4 !py-2">Install</button>
                <button onclick="document.getElementById('pwa-install-banner').classList.add('hidden')"
                        class="p-1 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        </div>

        @stack('scripts')
        @livewireScripts
    </body>
</html>
```

---

## FILE 6 — `resources/js/app.js`
**Problem:** PWA banner used `classList.remove('hidden')` which conflicts with the old Alpine `x-show`. Now that the banner uses plain `hidden` class without Alpine, the existing JS already works correctly. No logic change needed — but clean it up slightly.

```javascript
import './bootstrap';
import Alpine from 'alpinejs';

if (!window.Alpine) {
    window.Alpine = Alpine;
    Alpine.start();
}

if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
        navigator.serviceWorker.register('/sw.js').catch(() => {});
    });
}

let deferredPrompt;
window.addEventListener('beforeinstallprompt', (e) => {
    e.preventDefault();
    deferredPrompt = e;
    const banner = document.getElementById('pwa-install-banner');
    if (banner) banner.classList.remove('hidden');
});

window.installPwa = async () => {
    if (!deferredPrompt) return;
    deferredPrompt.prompt();
    await deferredPrompt.userChoice;
    deferredPrompt = null;
    const banner = document.getElementById('pwa-install-banner');
    if (banner) banner.classList.add('hidden');
};
```

---

## FILE 7 — `resources/views/lends/index.blade.php`
**Problem:** Lent Out and Borrowed are stacked vertically. Should be side-by-side on desktop.

```blade
<x-app-layout>
    <x-page-header title="My Lends" />

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                {{-- LENT OUT --}}
                <div class="card p-6 flex flex-col min-h-[24rem]">
                    <h3 class="section-title mb-4 flex items-center gap-2">
                        <span class="w-8 h-8 rounded-lg bg-seait-100 dark:bg-seait-900/30 flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-seait-600 dark:text-seait-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                            </svg>
                        </span>
                        Lent Out
                        @if (!$lentOut->isEmpty())
                            <span class="ml-auto text-xs font-medium px-2 py-0.5 rounded-full bg-seait-100 text-seait-700 dark:bg-seait-900/30 dark:text-seait-300">
                                {{ $lentOut->total() }}
                            </span>
                        @endif
                    </h3>

                    @if ($lentOut->isEmpty())
                        <div class="flex-1 flex flex-col items-center justify-center text-center py-8">
                            <div class="w-12 h-12 rounded-2xl bg-gray-100 dark:bg-navy-700/50 flex items-center justify-center mb-3">
                                <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                                </svg>
                            </div>
                            <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">Nothing lent out yet</p>
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Resources you lend will appear here.</p>
                        </div>
                    @else
                        <div class="space-y-3 flex-1">
                            @foreach ($lentOut as $lend)
                                <x-lend-row :lend="$lend" variant="lent" />
                            @endforeach
                        </div>
                        <div class="mt-4">{{ $lentOut->links() }}</div>
                    @endif
                </div>

                {{-- BORROWED --}}
                <div class="card p-6 flex flex-col min-h-[24rem]">
                    <h3 class="section-title mb-4 flex items-center gap-2">
                        <span class="w-8 h-8 rounded-lg bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
                            </svg>
                        </span>
                        Borrowed
                        @if (!$borrowed->isEmpty())
                            <span class="ml-auto text-xs font-medium px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300">
                                {{ $borrowed->total() }}
                            </span>
                        @endif
                    </h3>

                    @if ($borrowed->isEmpty())
                        <div class="flex-1 flex flex-col items-center justify-center text-center py-8">
                            <div class="w-12 h-12 rounded-2xl bg-gray-100 dark:bg-navy-700/50 flex items-center justify-center mb-3">
                                <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
                                </svg>
                            </div>
                            <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">Nothing borrowed yet</p>
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Resources you borrow will appear here.</p>
                        </div>
                    @else
                        <div class="space-y-3 flex-1">
                            @foreach ($borrowed as $lend)
                                <x-lend-row :lend="$lend" variant="borrowed" />
                            @endforeach
                        </div>
                        <div class="mt-4">{{ $borrowed->links() }}</div>
                    @endif
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
```

---

## FILE 8 — `resources/views/chat/index.blade.php`
**Problem:** Plain list with no visual hierarchy or invitation to enter a room.

```blade
<x-app-layout>
    <x-page-header title="{{ __('Chat') }}" />

    <div class="py-8">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">

            @if ($rooms->isEmpty())
                <div class="card p-12 text-center">
                    <div class="w-16 h-16 rounded-2xl bg-gray-100 dark:bg-navy-700/50 flex items-center justify-center mx-auto mb-4">
                        <x-icon name="chat" class="w-8 h-8 text-gray-400 dark:text-gray-500" />
                    </div>
                    <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100">No chat rooms yet</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Chat rooms are automatically created for your program.</p>
                </div>
            @else
                <p class="text-sm text-gray-500 dark:text-gray-400 px-1">
                    Rooms for
                    <span class="font-semibold text-gray-700 dark:text-gray-300">{{ auth()->user()->program?->code ?? 'your program' }}</span>
                </p>

                <div class="space-y-2">
                    @foreach ($rooms as $room)
                        <a href="{{ route('chat.show', $room) }}"
                           class="group flex items-center gap-4 p-4 card hover:shadow-md transition-all duration-200 hover:-translate-y-0.5">
                            <div class="w-11 h-11 rounded-2xl bg-gradient-to-br from-seait-400 to-seait-600 flex items-center justify-center flex-shrink-0 shadow-sm group-hover:scale-105 transition-transform">
                                <x-icon name="chat" class="w-5 h-5 text-white" />
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2">
                                    <span class="text-sm font-bold text-gray-900 dark:text-gray-100">#{{ $room->slug }}</span>
                                    <span class="text-[10px] font-medium px-2 py-0.5 rounded-full bg-gray-100 text-gray-500 dark:bg-navy-700 dark:text-gray-400 uppercase tracking-wide">
                                        {{ $room->kind->label() }}
                                    </span>
                                </div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5 truncate">{{ $room->title }}</p>
                            </div>
                            <svg class="w-4 h-4 text-gray-300 dark:text-gray-600 group-hover:text-seait-400 group-hover:translate-x-1 transition-all flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </a>
                    @endforeach
                </div>

                {{-- School chat etiquette notice --}}
                <div class="card p-4 border-l-4 border-amber-400 dark:border-amber-500 bg-amber-50/40 dark:bg-amber-900/10">
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-amber-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.054 0 1.502-1.275.722-1.845l-6.928-5.013c-.752-.545-1.792-.545-2.544 0L5.094 17.155c-.78.57-.332 1.845.722 1.845z"/>
                        </svg>
                        <div>
                            <p class="text-sm font-semibold text-amber-800 dark:text-amber-300">School Chat — Keep it respectful</p>
                            <p class="text-xs text-amber-700 dark:text-amber-400 mt-0.5">
                                Be kind, stay on topic, no spam. Inside any room tap the <strong>Rules</strong> button for the full etiquette guide.
                                All activity is subject to the <a href="{{ route('pages.aup') }}" class="underline hover:text-amber-900 dark:hover:text-amber-200">Acceptable Use Policy</a>.
                            </p>
                        </div>
                    </div>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>
```

---

## FILE 9 — `resources/views/chat/show.blade.php`
**Problem:** Chat is wrapped in `py-8` + card padding, causing the message area to be cramped and cut off. Redesign as a full-height layout without outer padding.

```blade
<x-app-layout>
    <div class="flex flex-col" style="height: calc(100vh - 4rem); overflow: hidden;">

        {{-- Chat header --}}
        <div class="flex items-center gap-3 px-4 sm:px-6 py-3 bg-white/90 dark:bg-navy-800/90 backdrop-blur-sm border-b border-gray-200/60 dark:border-navy-700/40 flex-shrink-0">
            <a href="{{ route('chat.index') }}"
               class="p-1.5 rounded-lg hover:bg-gray-100 dark:hover:bg-navy-700 transition-colors text-gray-500 dark:text-gray-400"
               title="All rooms">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-seait-400 to-seait-600 flex items-center justify-center flex-shrink-0">
                <x-icon name="chat" class="w-5 h-5 text-white" />
            </div>
            <div class="flex-1 min-w-0">
                <h1 class="text-base font-bold text-gray-900 dark:text-gray-100 leading-tight">#{{ $room->slug }}</h1>
                <p class="text-xs text-gray-500 dark:text-gray-400 truncate">{{ $room->title }} · {{ $room->kind->label() }}</p>
            </div>

            {{-- Rules panel --}}
            <div x-data="{ rulesOpen: false }" class="relative">
                <button @click="rulesOpen = !rulesOpen"
                        class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium text-amber-700 bg-amber-50 hover:bg-amber-100 dark:bg-amber-900/20 dark:text-amber-300 dark:hover:bg-amber-900/30 transition-colors border border-amber-200 dark:border-amber-800/40">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                    </svg>
                    Rules
                </button>
                <div x-show="rulesOpen"
                     x-transition:enter="transition ease-out duration-150"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-100"
                     x-transition:leave-start="opacity-100 scale-100"
                     x-transition:leave-end="opacity-0 scale-95"
                     @click.outside="rulesOpen = false"
                     class="absolute right-0 top-full mt-2 w-80 bg-white dark:bg-navy-800 rounded-2xl shadow-xl border border-gray-100 dark:border-navy-700/50 z-50 p-4"
                     x-cloak>
                    <h4 class="text-sm font-bold text-gray-900 dark:text-gray-100 mb-3 flex items-center gap-2">
                        <span class="text-base">📋</span> School Chat Etiquette
                    </h4>
                    <ul class="space-y-2.5">
                        @foreach ([
                            ['🎓', 'Be respectful', 'Treat everyone as you would in class. No bullying or hate speech.'],
                            ['📚', 'Stay on topic', 'Keep discussions relevant to academics and school life.'],
                            ['🔇', 'No spamming', 'Avoid repeated messages, all-caps, or emoji floods.'],
                            ['🔒', 'Protect privacy', 'Never share personal info or confidential documents.'],
                            ['⚠️', 'Report, don\'t retaliate', 'Use the Report button — don\'t escalate conflicts.'],
                            ['✅', 'Use @mentions wisely', 'Only @mention someone when your message is directly for them.'],
                        ] as [$emoji, $title, $desc])
                        <li class="flex items-start gap-2">
                            <span class="text-sm leading-none mt-0.5 flex-shrink-0">{{ $emoji }}</span>
                            <div>
                                <span class="text-xs font-semibold text-gray-900 dark:text-gray-100">{{ $title }}: </span>
                                <span class="text-xs text-gray-500 dark:text-gray-400">{{ $desc }}</span>
                            </div>
                        </li>
                        @endforeach
                    </ul>
                    <p class="mt-3 pt-3 border-t border-gray-100 dark:border-navy-700/50 text-[10px] text-gray-400 dark:text-gray-500">
                        Full policy: <a href="{{ route('pages.aup') }}" class="underline hover:text-seait-500">Acceptable Use Policy</a>
                    </p>
                </div>
            </div>
        </div>

        {{-- Livewire chat component fills remaining height --}}
        <div class="flex-1 overflow-hidden">
            <livewire:chat.room-conversation :room="$room" />
        </div>
    </div>
</x-app-layout>
```

---

## FILE 10 — `resources/views/livewire/chat/room-conversation.blade.php`
**Problems fixed:**
- `h-[28rem]` removed — messages area now fills available height via flex
- Auto-scrolls to bottom on new messages
- "New messages" button appears when scrolled up
- Grouped messages (consecutive messages from same sender don't repeat avatar/name)
- Deterministic color per user
- `Shift+Enter` for newline, `Enter` to send
- Auto-growing textarea
- Cancel button on report form

```blade
<div class="flex flex-col h-full" wire:poll.10s.visible>

    {{-- Messages --}}
    <div id="chat-log"
         role="log"
         aria-live="polite"
         data-testid="chat-message-list"
         class="flex-1 overflow-y-auto px-4 py-4 space-y-0.5 bg-gray-50/40 dark:bg-navy-900/20 scroll-smooth"
         x-data="{ atBottom: true }"
         x-ref="log"
         x-init="
            const el = $refs.log;
            el.scrollTop = el.scrollHeight;
            new MutationObserver(() => { if (atBottom) el.scrollTop = el.scrollHeight; }).observe(el, { childList: true, subtree: true });
            el.addEventListener('scroll', () => { atBottom = (el.scrollHeight - el.scrollTop - el.clientHeight) < 100; });
         ">

        @php
            $colors = ['from-violet-400 to-violet-600','from-sky-400 to-sky-600','from-emerald-400 to-emerald-600','from-rose-400 to-rose-600','from-amber-400 to-amber-600','from-fuchsia-400 to-fuchsia-600','from-cyan-400 to-cyan-600','from-teal-400 to-teal-600'];
            $messages = $this->roomMessages;
        @endphp

        @forelse ($messages as $index => $message)
            @php
                $prev = $messages[$index - 1] ?? null;
                $showHeader = !$prev || $prev->sender_id !== $message->sender_id || $message->created_at->diffInMinutes($prev->created_at) > 3;
                $colorClass = $colors[$message->sender_id % count($colors)];
                $isOwn = $message->sender_id === Auth::id();
            @endphp

            <article data-testid="chat-message"
                     wire:key="message-{{ $message->id }}"
                     class="group {{ $showHeader ? 'mt-4 first:mt-0' : 'mt-0.5' }}">

                @if ($showHeader)
                    <div class="flex items-start gap-3">
                        <div class="flex-shrink-0 w-8 h-8 rounded-full bg-gradient-to-br {{ $colorClass }} flex items-center justify-center text-white text-xs font-bold shadow-sm">
                            {{ strtoupper(substr($message->sender?->preferredDisplayName() ?? '?', 0, 2)) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-baseline gap-2 flex-wrap mb-1">
                                <span class="text-sm font-bold {{ $isOwn ? 'text-seait-600 dark:text-seait-400' : 'text-gray-900 dark:text-gray-100' }}">
                                    {{ $message->sender?->preferredDisplayName() ?? 'Unknown' }}
                                </span>
                                @if ($message->sender?->program?->code)
                                    <span class="badge-seait text-[10px]">{{ $message->sender->program->code }}{{ $message->sender->year_level ? ' Y'.$message->sender->year_level : '' }}</span>
                                @endif
                                <time class="text-[11px] text-gray-400 dark:text-gray-500"
                                      title="{{ $message->created_at?->format('M d, Y g:i A') }}">
                                    {{ $message->created_at?->diffForHumans() }}
                                </time>
                            </div>
                            <div class="text-sm text-gray-700 dark:text-gray-200 whitespace-pre-wrap leading-relaxed break-words">{{ $message->body }}</div>
                            @if ($message->hasAttachment())
                                <a href="{{ $message->attachment_url }}" target="_blank" rel="noopener"
                                   class="mt-2 inline-flex items-center gap-1.5 text-xs text-seait-600 bg-seait-50 hover:bg-seait-100 px-2.5 py-1 rounded-lg dark:text-seait-400 dark:bg-seait-900/20 transition-colors">
                                    <x-icon name="attachment" class="w-3.5 h-3.5" /> Attachment
                                </a>
                            @endif
                        </div>
                        <div class="opacity-0 group-hover:opacity-100 transition-opacity flex-shrink-0 mt-0.5">
                            <button type="button"
                                    onclick="document.getElementById('rf-{{ $message->id }}').classList.toggle('hidden')"
                                    class="p-1 rounded text-gray-300 hover:text-red-400 dark:text-gray-600 dark:hover:text-red-400 transition-colors"
                                    title="Report">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9"/></svg>
                            </button>
                        </div>
                    </div>
                @else
                    {{-- Compact continuation --}}
                    <div class="flex items-start gap-3">
                        <div class="w-8 flex-shrink-0 flex items-center justify-center">
                            <time class="text-[10px] text-gray-300 dark:text-gray-600 opacity-0 group-hover:opacity-100 transition-opacity">{{ $message->created_at?->format('g:i') }}</time>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="text-sm text-gray-700 dark:text-gray-200 whitespace-pre-wrap leading-relaxed break-words">{{ $message->body }}</div>
                            @if ($message->hasAttachment())
                                <a href="{{ $message->attachment_url }}" target="_blank" rel="noopener"
                                   class="mt-1.5 inline-flex items-center gap-1.5 text-xs text-seait-600 bg-seait-50 px-2.5 py-1 rounded-lg dark:text-seait-400 dark:bg-seait-900/20 transition-colors">
                                    <x-icon name="attachment" class="w-3.5 h-3.5" /> Attachment
                                </a>
                            @endif
                        </div>
                        <div class="opacity-0 group-hover:opacity-100 transition-opacity flex-shrink-0">
                            <button type="button"
                                    onclick="document.getElementById('rf-{{ $message->id }}').classList.toggle('hidden')"
                                    class="p-1 rounded text-gray-300 hover:text-red-400 dark:text-gray-600 dark:hover:text-red-400 transition-colors"
                                    title="Report">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9"/></svg>
                            </button>
                        </div>
                    </div>
                @endif

                {{-- Report form --}}
                <form id="rf-{{ $message->id }}" method="POST" action="{{ route('reports.store') }}"
                      class="hidden mt-2 ml-11 p-3 rounded-xl bg-red-50/70 dark:bg-red-900/10 border border-red-100 dark:border-red-800/20 space-y-2">
                    @csrf
                    <input type="hidden" name="reported_type" value="message">
                    <input type="hidden" name="reported_id" value="{{ $message->id }}">
                    <p class="text-xs font-semibold text-red-700 dark:text-red-400">Report message</p>
                    <select name="reason" required class="text-xs input-field w-full">
                        <option value="">Select reason…</option>
                        @foreach (\App\Domain\Moderation\Enums\ReportReason::cases() as $reason)
                            <option value="{{ $reason->value }}">{{ $reason->label() }}</option>
                        @endforeach
                    </select>
                    <div class="flex gap-2">
                        <input type="text" name="notes" maxlength="1000" placeholder="Optional note" class="text-xs input-field flex-1">
                        <button type="submit" onclick="this.disabled=true;this.form.submit();"
                                class="px-3 py-1.5 bg-red-500 hover:bg-red-600 text-white text-xs font-semibold rounded-lg transition-colors disabled:opacity-50">Submit</button>
                        <button type="button" onclick="this.closest('form').classList.add('hidden')"
                                class="px-3 py-1.5 border border-gray-200 dark:border-navy-600 text-xs text-gray-600 dark:text-gray-400 rounded-lg hover:bg-gray-100 dark:hover:bg-navy-700 transition-colors">Cancel</button>
                    </div>
                </form>
            </article>
        @empty
            <div class="flex flex-col items-center justify-center h-full text-center py-16">
                <div class="w-20 h-20 rounded-3xl bg-gradient-to-br from-seait-100 to-seait-200 dark:from-seait-900/30 dark:to-seait-800/20 flex items-center justify-center mb-4">
                    <x-icon name="chat" class="w-10 h-10 text-seait-400 dark:text-seait-500" />
                </div>
                <p class="text-base font-semibold text-gray-700 dark:text-gray-300" data-testid="chat-empty-state">No messages yet</p>
                <p class="text-sm text-gray-400 dark:text-gray-500 mt-1">Be the first to say something! 👋</p>
            </div>
        @endforelse

        {{-- "Scroll to bottom" button --}}
        <div x-data="{ atBottom: true }"
             x-init="
                const log = document.getElementById('chat-log');
                if (log) {
                    log.addEventListener('scroll', () => { atBottom = (log.scrollHeight - log.scrollTop - log.clientHeight) < 100; });
                }
             "
             class="sticky bottom-2 flex justify-center pointer-events-none">
            <button x-show="!atBottom"
                    @click="const l=document.getElementById('chat-log'); l.scrollTop=l.scrollHeight; atBottom=true;"
                    class="pointer-events-auto flex items-center gap-1.5 px-3 py-1.5 bg-seait-500 text-white text-xs font-medium rounded-full shadow-lg hover:bg-seait-600 transition-colors"
                    x-cloak>
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/></svg>
                Jump to latest
            </button>
        </div>
    </div>

    {{-- Input bar --}}
    <div class="flex-shrink-0 border-t border-gray-200/60 dark:border-navy-700/40 bg-white/80 dark:bg-navy-800/80 backdrop-blur-sm px-4 py-3">
        <form wire:submit="send" class="flex items-end gap-2">
            <label class="flex-shrink-0 cursor-pointer p-2.5 rounded-xl hover:bg-gray-100 dark:hover:bg-navy-700 transition-colors text-gray-400 hover:text-gray-600 border border-gray-200 dark:border-navy-700" title="Attach file">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                <input type="file" wire:model="attachment" accept="image/*,application/pdf" class="hidden">
            </label>
            <div class="flex-1 relative">
                <label for="chat-body" class="sr-only">Message</label>
                <textarea id="chat-body"
                          wire:model="body"
                          rows="1"
                          class="input-field resize-none w-full max-h-32 overflow-y-auto !py-2.5"
                          placeholder="Send a message… @name to mention"
                          @keydown.enter.prevent="if(!$event.shiftKey){$wire.send()}else{$event.target.value+='\n'}"
                          oninput="this.style.height='auto';this.style.height=Math.min(this.scrollHeight,128)+'px'"></textarea>
            </div>
            <button type="submit" wire:loading.attr="disabled"
                    class="flex-shrink-0 p-2.5 rounded-xl bg-seait-500 hover:bg-seait-600 text-white transition-colors disabled:opacity-50 shadow-sm">
                <x-icon wire:loading.remove.delay name="send" class="w-5 h-5" />
                <x-icon wire:loading name="spinner" class="w-5 h-5 animate-spin" />
            </button>
        </form>
        @error('body') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
        @error('attachment') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
        <p class="mt-1 text-[10px] text-gray-400 dark:text-gray-600">
            <kbd class="px-1 rounded bg-gray-100 dark:bg-navy-700 font-mono text-[9px]">Enter</kbd> send &nbsp;·&nbsp;
            <kbd class="px-1 rounded bg-gray-100 dark:bg-navy-700 font-mono text-[9px]">Shift+Enter</kbd> new line
        </p>
    </div>
</div>
```

---

## FILE 11 — `resources/views/profile/edit.blade.php`
**Problem:** No link to Notification Preferences from Account Settings. Added as a fourth card.

```blade
<x-app-layout>
    <x-page-header title="{{ __('Account Settings') }}" />

    <div class="py-8">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            <div class="card p-4 sm:p-8">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <div class="card p-4 sm:p-8">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            {{-- Notification Preferences shortcut --}}
            <div class="card p-6">
                <div class="flex items-center justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center flex-shrink-0">
                            <x-icon name="notifications" class="w-5 h-5 text-blue-600 dark:text-blue-400" />
                        </div>
                        <div>
                            <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Notification Preferences</h3>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Control which notifications you receive and how often.</p>
                        </div>
                    </div>
                    <a href="{{ route('profile.notification-preferences') }}" class="btn-secondary text-xs flex-shrink-0">
                        Manage
                    </a>
                </div>
            </div>

            <div class="card p-4 sm:p-8">
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
```

---

## SUMMARY TABLE

| # | File | What changed |
|---|------|-------------|
| 1 | `components/dropdown-link.blade.php` | `flex items-center` fixes icon alignment |
| 2 | `components/application-logo.blade.php` | New organic graduation cap + book logo |
| 3 | `public/favicon.svg` | New matching favicon |
| 4 | `layouts/navigation.blade.php` | Logout modal · Notification polling · Notif. Settings link · Mobile logout modal · New logo |
| 5 | `layouts/app.blade.php` | `$errors` auto-dismiss · PWA banner uses `hidden` class not Alpine `x-show` · Header padding increased |
| 6 | `resources/js/app.js` | Minor cleanup — logic already correct for plain `hidden` class |
| 7 | `lends/index.blade.php` | Side-by-side grid layout instead of vertical stack |
| 8 | `chat/index.blade.php` | Card-style room list with etiquette notice |
| 9 | `chat/show.blade.php` | Full-height flex layout, Rules panel, back button |
| 10 | `livewire/chat/room-conversation.blade.php` | No more `h-[28rem]` cutoff · auto-scroll · grouped messages · grow textarea · `Shift+Enter` |
| 11 | `profile/edit.blade.php` | Notification Preferences card added, title updated to "Account Settings" |
