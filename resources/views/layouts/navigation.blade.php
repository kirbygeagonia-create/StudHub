@php $authUser = $authUser ?? Auth::user(); @endphp
<div x-data="{ open: false, scrolled: false, logoutOpen: false }">
<nav @scroll.window="scrolled = window.scrollY > 10" :class="{'shadow-md': scrolled}" class="sticky top-0 z-40 bg-white/95 dark:bg-navy-900/95 backdrop-blur-xl border-b border-gray-200/50 dark:border-navy-700/30" role="navigation" aria-label="Main navigation">
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
                    <x-nav-link :href="route('requests.index')" :active="request()->routeIs('requests.*')">
                        <x-icon name="search" class="w-4 h-4 mr-1.5" />
                        Requests
                    </x-nav-link>
                    <x-nav-link :href="route('lends.index')" :active="request()->routeIs('lends.*')">
                        <x-icon name="lends" class="w-4 h-4 mr-1.5" />
                        Lends
                    </x-nav-link>
                    @if ($authUser?->isModerator() || $authUser?->isProgramHead() || $authUser?->isDean() || $authUser?->isSao() || $authUser?->isSuperAdmin())
                        <x-nav-link :href="route('moderation.dashboard')" :active="request()->routeIs('moderation.*')">
                            <x-icon name="moderation" class="w-4 h-4 mr-1.5" />
                            Moderation
                        </x-nav-link>
                    @endif
                    @if ($authUser?->isProgramHead())
                        <x-nav-link :href="route('program_head.dashboard')" :active="request()->routeIs('program_head.*')">
                            <x-icon name="admin" class="w-4 h-4 mr-1.5" />
                            Program Head
                        </x-nav-link>
                    @endif
                    @if ($authUser?->isDean())
                        <x-nav-link :href="route('dean.dashboard')" :active="request()->routeIs('dean.*')">
                            <x-icon name="admin" class="w-4 h-4 mr-1.5" />
                            Dean Panel
                        </x-nav-link>
                    @endif
                    @if ($authUser?->isSao())
                        <x-nav-link :href="route('sao.dashboard')" :active="request()->routeIs('sao.*')">
                            <x-icon name="admin" class="w-4 h-4 mr-1.5" />
                            SAO Panel
                        </x-nav-link>
                    @endif
                    @if ($authUser?->isSuperAdmin())
                        <x-nav-link :href="route('sao.dashboard')" :active="request()->routeIs('sao.*')">
                            <x-icon name="admin" class="w-4 h-4 mr-1.5" />
                            System
                        </x-nav-link>
                    @endif
                </div>
            </div>

            <div class="hidden sm:flex sm:items-center sm:gap-2">
                <div class="relative" x-data="{
    q: '{{ request('q') }}',
    results: null,
    searching: false,
    open: false,
    search(val) {
        if (val.length < 3) { this.results = null; this.open = false; return; }
        this.searching = true;
        clearTimeout(this._t);
        this._t = setTimeout(() => {
            fetch('/search/inline?q=' + encodeURIComponent(val), {
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
                credentials: 'same-origin',
            })
            .then(r => r.ok ? r.json() : Promise.reject(r.status))
            .then(data => { this.results = data; this.open = true; this.searching = false; })
            .catch(() => { this.searching = false; });
        }, 350);
    },
    close() {
        this.open = false;
        setTimeout(() => { this.results = null; }, 200);
    }
}" role="search">
    <label for="nav-search" class="sr-only">Search resources, requests, messages</label>
    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
        <template x-if="!searching">
            <x-icon name="search" class="h-4 w-4 text-gray-400" />
        </template>
        <template x-if="searching">
            <svg class="h-4 w-4 text-seait-500 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
        </template>
    </div>
    <input type="text" id="nav-search" name="q" x-model="q"
           @input.debounce="search(q)"
           @keydown.escape.window="close()"
           @click.outside="close()"
           @keydown.enter="if (q.length >= 3) window.location.href = '/search?q=' + encodeURIComponent(q)"
           placeholder="Search… (min 3 chars)"
           class="w-48 pl-10 pr-3 py-2 text-sm rounded-xl border-gray-200 bg-gray-50/80 focus:bg-white focus:border-seait-400 focus:ring-2 focus:ring-seait-100 transition-all placeholder:text-gray-400 dark:bg-navy-800/60 dark:border-navy-700/50 dark:text-gray-200 dark:placeholder:text-gray-500 dark:focus:border-seait-500 dark:focus:ring-seait-800/30">
    
    <!-- Dropdown -->
    <div x-show="open && results"
         x-transition:enter="transition ease-out duration-1500"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-100"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="absolute right-0 mt-2 w-96 bg-white dark:bg-navy-800 rounded-2xl shadow-xl border border-gray-100 dark:border-navy-700/50 z-50 overflow-hidden"
         x-cloak>
        <div class="max-h-96 overflow-y-auto divide-y divide-gray-100 dark:divide-navy-700/30">
            <!-- Resources -->
            <div x-show="results.resources.length > 0" class="p-2">
                <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider px-2 py-1">Resources</p>
                <template x-for="r in results.resources" :key="'r' + r.id">
                    <a :href="r.url" class="flex items-center gap-3 px-2 py-2 rounded-xl hover:bg-gray-50 dark:hover:bg-navy-700/50 transition-colors">
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate" x-text="r.title"></p>
                            <p class="text-xs text-gray-500 dark:text-gray-400" x-text="r.type"></p>
                        </div>
                    </a>
                </template>
            </div>
            <!-- Requests -->
            <div x-show="results.requests.length > 0" class="p-2">
                <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider px-2 py-1">Requests</p>
                <template x-for="r in results.requests" :key="'req' + r.id">
                    <a :href="r.url" class="flex items-center gap-3 px-2 py-2 rounded-xl hover:bg-gray-50 dark:hover:bg-navy-700/50 transition-colors">
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate" x-text="r.description"></p>
                        </div>
                    </a>
                </template>
            </div>
            <!-- Messages -->
            <div x-show="results.messages.length > 0" class="p-2">
                <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider px-2 py-1">Messages</p>
                <template x-for="m in results.messages" :key="'m' + m.id">
                    <a :href="m.url" class="flex items-center gap-3 px-2 py-2 rounded-xl hover:bg-gray-50 dark:hover:bg-navy-700/50 transition-colors">
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate" x-text="m.body"></p>
                            <p class="text-xs text-gray-500 dark:text-gray-400" x-text="'in ' + m.room"></p>
                        </div>
                    </a>
                </template>
            </div>
            <!-- No results -->
            <div x-show="results.resources.length === 0 && results.requests.length === 0 && results.messages.length === 0" class="px-4 py-8 text-center">
                <p class="text-sm text-gray-400 dark:text-gray-500">No results found</p>
            </div>
        </div>
        <div class="px-4 py-2.5 border-t border-gray-100 dark:border-navy-700/50 bg-gray-50/50 dark:bg-navy-800/50">
            <a :href="'/search?q=' + encodeURIComponent(q)" class="text-xs font-medium text-seait-600 hover:text-seait-700 dark:text-seait-400 dark:hover:text-seait-300 transition-colors">See all results →</a>
        </div>
    </div>
</div>
                </div>

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

                <x-dropdown align="right" width="56">
                    <x-slot name="trigger">
                        <button class="flex items-center gap-2 px-3 py-2 rounded-xl hover:bg-gray-50/80 dark:hover:bg-navy-800/60 transition-all duration-200">
                            <div class="w-8 h-8 rounded-full bg-gradient-to-br from-seait-400 to-seait-600 flex items-center justify-center text-white text-xs font-bold ring-2 ring-seait-100 dark:ring-seait-800/40 shadow-sm">
                                {{ strtoupper(substr($authUser?->preferredDisplayName() ?? '?', 0, 2)) }}
                            </div>
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300 hidden lg:block">{{ $authUser?->preferredDisplayName() ?? 'Guest' }}</span>
                            <x-icon name="chevron-down" class="h-4 w-4 text-gray-400" />
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <div class="px-3 py-2.5 border-b border-gray-100 dark:border-navy-700/50">
                            <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $authUser?->name }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $authUser?->email }}</p>
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
                            <x-dropdown-link :href="route('requests.index')">
                                <x-icon name="search" class="w-4 h-4 mr-2 flex-shrink-0" />
                                My Requests
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('notifications.index')">
                                <x-icon name="notifications" class="w-4 h-4 mr-2 flex-shrink-0" />
                                Notifications
                            </x-dropdown-link>
                            @if ($authUser?->isProgramHead())
                                <x-dropdown-link :href="route('program_head.feedback')">
                                    <x-icon name="feedback" class="w-4 h-4 mr-2 flex-shrink-0" />
                                    View Feedback
                                </x-dropdown-link>
                            @elseif ($authUser?->isDean())
                                <x-dropdown-link :href="route('dean.feedback')">
                                    <x-icon name="feedback" class="w-4 h-4 mr-2 flex-shrink-0" />
                                    View Feedback
                                </x-dropdown-link>
                            @elseif ($authUser?->isSao() || $authUser?->isSuperAdmin())
                                <x-dropdown-link :href="route('sao.feedback')">
                                    <x-icon name="feedback" class="w-4 h-4 mr-2 flex-shrink-0" />
                                    View Feedback
                                </x-dropdown-link>
                            @else
                                <x-dropdown-link :href="route('feedback.create')">
                                    <x-icon name="feedback" class="w-4 h-4 mr-2 flex-shrink-0" />
                                    Feedback
                                </x-dropdown-link>
                            @endif
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
                <x-responsive-nav-link :href="route('requests.index')" :active="request()->routeIs('requests.*')">Requests</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('lends.index')" :active="request()->routeIs('lends.*')">Lends</x-responsive-nav-link>
                @if ($authUser?->isModerator() || $authUser?->isProgramHead() || $authUser?->isDean() || $authUser?->isSao() || $authUser?->isSuperAdmin())
                    <x-responsive-nav-link :href="route('moderation.dashboard')" :active="request()->routeIs('moderation.*')">Moderation</x-responsive-nav-link>
                @endif
                @if ($authUser?->isProgramHead())
                    <x-responsive-nav-link :href="route('program_head.dashboard')" :active="request()->routeIs('program_head.*')">Program Head</x-responsive-nav-link>
                @endif
                @if ($authUser?->isDean())
                    <x-responsive-nav-link :href="route('dean.dashboard')" :active="request()->routeIs('dean.*')">Dean Panel</x-responsive-nav-link>
                @endif
                @if ($authUser?->isSao())
                    <x-responsive-nav-link :href="route('sao.dashboard')" :active="request()->routeIs('sao.*')">SAO Panel</x-responsive-nav-link>
                @endif
                @if ($authUser?->isSuperAdmin())
                    <x-responsive-nav-link :href="route('sao.dashboard')" :active="request()->routeIs('sao.*')">System</x-responsive-nav-link>
                @endif
            </div>
            <div class="border-t border-gray-100 dark:border-navy-700/50 px-3 py-3 space-y-1">
                <div class="px-3 pb-2">
                    <p class="font-medium text-sm text-gray-900 dark:text-gray-100">{{ $authUser?->name ?? 'Guest' }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $authUser?->email ?? '' }}</p>
                </div>
                <x-responsive-nav-link :href="route('profile.show')">Profile</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('resources.shelf')">My Shelf</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('requests.index')">My Requests</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('notifications.index')">Notifications</x-responsive-nav-link>
                @if ($authUser?->isProgramHead())
                    <x-responsive-nav-link :href="route('program_head.feedback')">View Feedback</x-responsive-nav-link>
                @elseif ($authUser?->isDean())
                    <x-responsive-nav-link :href="route('dean.feedback')">View Feedback</x-responsive-nav-link>
                @elseif ($authUser?->isSao() || $authUser?->isSuperAdmin())
                    <x-responsive-nav-link :href="route('sao.feedback')">View Feedback</x-responsive-nav-link>
                @else
                    <x-responsive-nav-link :href="route('feedback.create')">Feedback</x-responsive-nav-link>
                @endif
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

    </nav>

<!-- Logout Confirmation Modal (outside nav to avoid sticky/z-index conflicts) -->
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

{{-- Mobile bottom tab bar --}}
<div class="fixed bottom-0 inset-x-0 z-50 sm:hidden bg-white/95 dark:bg-navy-900/95 backdrop-blur-xl border-t border-gray-200/50 dark:border-navy-700/30">
    <div class="flex items-center justify-around py-1.5">
        <a href="{{ route('dashboard') }}" class="flex flex-col items-center gap-0.5 px-3 py-1 rounded-lg {{ request()->routeIs('dashboard') ? 'text-seait-500' : 'text-gray-400 dark:text-gray-500' }}" aria-label="Home">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
            <span class="text-[10px] font-medium">Home</span>
        </a>
        <a href="{{ route('chat.index') }}" class="flex flex-col items-center gap-0.5 px-3 py-1 rounded-lg {{ request()->routeIs('chat.*') ? 'text-seait-500' : 'text-gray-400 dark:text-gray-500' }}" aria-label="Chat">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
            <span class="text-[10px] font-medium">Chat</span>
        </a>
        <a href="{{ route('resources.index') }}" class="flex flex-col items-center gap-0.5 px-3 py-1 rounded-lg {{ request()->routeIs('resources.*') ? 'text-seait-500' : 'text-gray-400 dark:text-gray-500' }}" aria-label="Resources">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
            <span class="text-[10px] font-medium">Resources</span>
        </a>
        <a href="{{ route('requests.index') }}" class="flex flex-col items-center gap-0.5 px-3 py-1 rounded-lg {{ request()->routeIs('requests.*') ? 'text-seait-500' : 'text-gray-400 dark:text-gray-500' }}" aria-label="Requests">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            <span class="text-[10px] font-medium">Requests</span>
        </a>
        <a href="{{ route('lends.index') }}" class="flex flex-col items-center gap-0.5 px-3 py-1 rounded-lg {{ request()->routeIs('lends.*') ? 'text-seait-500' : 'text-gray-400 dark:text-gray-500' }}" aria-label="Lends">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
            <span class="text-[10px] font-medium">Lends</span>
        </a>
    </div>
</div>
</div>