<nav x-data="{ open: false, scrolled: false }" @scroll.window="scrolled = window.scrollY > 10" :class="{'shadow-md': scrolled}" class="sticky top-0 z-40 bg-white/95 dark:bg-navy-900/95 backdrop-blur-xl border-b border-gray-200/50 dark:border-navy-700/30" role="navigation" aria-label="Main navigation">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center gap-1">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5 mr-6 group">
                    <!-- StudHub Brand Logo -->
                    <div class="w-8 h-8 flex items-center justify-center flex-shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32" fill="none" class="w-8 h-8">
                            <defs>
                                <linearGradient id="shLogoGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                                    <stop offset="0%" style="stop-color:#FF6B35"/>
                                    <stop offset="100%" style="stop-color:#E5512A"/>
                                </linearGradient>
                                <linearGradient id="shLogoGradLight" x1="0%" y1="100%" x2="100%" y2="0%">
                                    <stop offset="0%" style="stop-color:#FF8C5A"/>
                                    <stop offset="100%" style="stop-color:#FF6B35"/>
                                </linearGradient>
                            </defs>
                            <!-- Outer glow ring -->
                            <circle cx="16" cy="16" r="14" stroke="url(#shLogoGrad)" stroke-width="0.5" fill="none" opacity="0.2"/>
                            <!-- Main book shape -->
                            <path d="M9 7h14c1.105 0 2 .895 2 2v16c0 1.105-.895 2-2 2H9c-1.105 0-2-.895-2-2V9c0-1.105.895-2 2-2z" fill="url(#shLogoGrad)" opacity="0.95"/>
                            <!-- Book spine -->
                            <path d="M16 7v20" stroke="white" stroke-width="0.5" opacity="0.3"/>
                            <!-- Left page lines -->
                            <line x1="10.5" y1="11" x2="14" y2="11" stroke="white" stroke-width="0.75" opacity="0.5" stroke-linecap="round"/>
                            <line x1="10.5" y1="14" x2="14" y2="14" stroke="white" stroke-width="0.75" opacity="0.5" stroke-linecap="round"/>
                            <line x1="10.5" y1="17" x2="13" y2="17" stroke="white" stroke-width="0.75" opacity="0.4" stroke-linecap="round"/>
                            <!-- Right page lines -->
                            <line x1="18" y1="11" x2="21.5" y2="11" stroke="white" stroke-width="0.75" opacity="0.5" stroke-linecap="round"/>
                            <line x1="18" y1="14" x2="21.5" y2="14" stroke="white" stroke-width="0.75" opacity="0.5" stroke-linecap="round"/>
                            <line x1="18" y1="17" x2="20.5" y2="17" stroke="white" stroke-width="0.75" opacity="0.4" stroke-linecap="round"/>
                            <!-- Bookmark ribbon -->
                            <path d="M16 7l-1 3h2z" fill="#FF8C5A"/>
                            <!-- Decorative dots -->
                            <circle cx="16" cy="4" r="1" fill="url(#shLogoGradLight)"/>
                            <circle cx="27" cy="10" r="1" fill="url(#shLogoGradLight)"/>
                            <circle cx="5" cy="10" r="1" fill="url(#shLogoGradLight)"/>
                            <circle cx="16" cy="28" r="1" fill="url(#shLogoGradLight)"/>
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
                <div class="relative" x-data="{ notifOpen: false, notifications: [
                    { id: 1, type: 'badge', title: 'New badge earned: First Review', time: '2 min ago', icon: 'star', color: 'amber', read: false },
                    { id: 2, type: 'request', title: 'Your request was accepted', time: '15 min ago', icon: 'check', color: 'emerald', read: false },
                    { id: 3, type: 'lend', title: 'Return reminder: Calculus Notes', time: '1 hr ago', icon: 'clock', color: 'blue', read: true }
                ] }">
                    <button @click="notifOpen = !notifOpen" @click.away="notifOpen = false" class="relative p-2 rounded-xl hover:bg-gray-50/80 dark:hover:bg-navy-800/60 transition-all duration-200" aria-label="Notifications">
                        <x-icon name="notifications" class="w-5 h-5 text-gray-600 dark:text-gray-300" />
                        <span class="absolute top-1 right-1 w-4 h-4 bg-red-500 text-white text-[10px] font-bold rounded-full flex items-center justify-center ring-2 ring-white dark:ring-navy-900">3</span>
                    </button>
                    <!-- Notifications Dropdown -->
                    <div x-show="notifOpen" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95" class="absolute right-0 mt-2 w-80 bg-white dark:bg-navy-800 rounded-2xl shadow-xl border border-gray-100 dark:border-navy-700/50 z-50 overflow-hidden" x-cloak>
                        <div class="px-4 py-3 border-b border-gray-100 dark:border-navy-700/50 flex items-center justify-between">
                            <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Notifications</h3>
                            <span class="text-xs text-gray-500 dark:text-gray-400">3 unread</span>
                        </div>
                        <div class="max-h-72 overflow-y-auto">
                            <template x-for="notif in notifications" :key="notif.id">
                                <a href="#" class="flex items-start gap-3 px-4 py-3 hover:bg-gray-50 dark:hover:bg-navy-700/50 transition-colors border-b border-gray-50 dark:border-navy-700/30 last:border-b-0">
                                    <div :class="`w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0 ${notif.color === 'amber' ? 'bg-amber-100 text-amber-600 dark:bg-amber-900/30 dark:text-amber-300' : notif.color === 'emerald' ? 'bg-emerald-100 text-emerald-600 dark:bg-emerald-900/30 dark:text-emerald-300' : 'bg-blue-100 text-blue-600 dark:bg-blue-900/30 dark:text-blue-300'}`">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-html="notif.icon === 'star' ? '<path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z\'/>' : notif.icon === 'check' ? '<path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z\'/>' : '<path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z\'/>'"></svg>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900 dark:text-gray-100" x-text="notif.title"></p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5" x-text="notif.time"></p>
                                    </div>
                                    <div x-show="!notif.read" class="w-2 h-2 bg-seait-500 rounded-full flex-shrink-0 mt-1.5"></div>
                                </a>
                            </template>
                        </div>
                        <div class="px-4 py-2.5 border-t border-gray-100 dark:border-navy-700/50 bg-gray-50/50 dark:bg-navy-800/50">
                            <a href="{{ route('notifications.index') }}" class="text-xs font-medium text-seait-600 hover:text-seait-700 dark:text-seait-400 dark:hover:text-seait-300 transition-colors">View all notifications</a>
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
                                <x-icon name="profile" class="w-4 h-4 mr-2" />
                                Profile
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('resources.shelf')">
                                <x-icon name="shelf" class="w-4 h-4 mr-2" />
                                My Shelf
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('notifications.index')">
                                <x-icon name="notifications" class="w-4 h-4 mr-2" />
                                Notifications
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('profile.notification-preferences')">
                                <x-icon name="settings" class="w-4 h-4 mr-2" />
                                Notification Settings
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('feedback.create')">
                                <x-icon name="feedback" class="w-4 h-4 mr-2" />
                                Feedback
                            </x-dropdown-link>
                        </div>

                        <div class="border-t border-gray-100 dark:border-navy-700/50 py-1">
                            <button @click="dark = !dark" type="button"
                                    class="w-full text-left flex items-center px-3 py-2 text-sm text-gray-700 hover:bg-gray-50/80 dark:text-gray-300 dark:hover:bg-navy-800/60 transition-colors">
                                <template x-if="!dark">
                                    <x-icon name="dark" class="w-4 h-4 mr-2" />
                                </template>
                                <template x-if="dark">
                                    <x-icon name="light" class="w-4 h-4 mr-2" />
                                </template>
                                <span x-text="dark ? 'Light mode' : 'Dark mode'"></span>
                            </button>
                        </div>

                        <div class="border-t border-gray-100 dark:border-navy-700/50 py-1">
                            <form method="POST" action="{{ route('logout') }}" x-data="{ showLogoutModal: false }">
                                @csrf
                                <x-dropdown-link :href="route('logout')"
                                        onclick="event.preventDefault(); showLogoutModal = true">
                                    <x-icon name="logout" class="w-4 h-4 mr-2" />
                                    Log Out
                                </x-dropdown-link>
                                <!-- Logout Confirmation Modal -->
                                <div x-show="showLogoutModal" class="fixed inset-0 z-[100]" x-cloak>
                                    <!-- Backdrop -->
                                    <div x-show="showLogoutModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-black/40 backdrop-blur-sm"></div>
                                    <!-- Modal -->
                                    <div class="fixed inset-0 z-10 flex items-center justify-center p-4">
                                        <div x-show="showLogoutModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95" class="bg-white dark:bg-navy-800 rounded-2xl shadow-2xl border border-gray-100 dark:border-navy-700/50 w-full max-w-sm p-6" @click.away="showLogoutModal = false">
                                            <div class="flex items-center gap-3 mb-4">
                                                <div class="w-10 h-10 rounded-full bg-red-100 dark:bg-red-900/30 flex items-center justify-center flex-shrink-0">
                                                    <svg class="w-5 h-5 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                                </div>
                                                <div>
                                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Log Out</h3>
                                                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">You will be redirected to the login page.</p>
                                                </div>
                                            </div>
                                            <div class="flex items-center justify-end gap-3 mt-6">
                                                <button type="button" @click="showLogoutModal = false" class="btn-secondary text-xs">Cancel</button>
                                                <button type="submit" onclick="this.closest('form').submit()" class="btn-danger text-xs">Log Out</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
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
                <x-responsive-nav-link :href="route('profile.notification-preferences')">Notification Settings</x-responsive-nav-link>
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
                <form method="POST" action="{{ route('logout') }}" class="px-3">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault(); this.closest('form').submit();">Log Out</x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
