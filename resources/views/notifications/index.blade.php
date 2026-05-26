<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-900 dark:text-gray-100">Notifications</h2>
    </x-slot>

    <div class="py-8" x-data="{
        activeFilter: 'all',
        notifications: [
            { id: 1, type: 'badge', title: 'New badge earned: First Review', description: 'You earned the First Review badge for reviewing a resource.', time: '2 min ago', icon: 'star', color: 'amber', read: false, date: 'Today' },
            { id: 2, type: 'request', title: 'Your request was accepted', description: 'Your request for Calculus Notes has been accepted by John Doe.', time: '15 min ago', icon: 'check', color: 'emerald', read: false, date: 'Today' },
            { id: 3, type: 'lend', title: 'Return reminder: Calculus Notes', description: 'Your borrowed resource is due for return in 2 days.', time: '1 hr ago', icon: 'clock', color: 'blue', read: true, date: 'Today' },
            { id: 4, type: 'badge', title: 'New badge earned: Helpful Contributor', description: 'You earned the Helpful Contributor badge for 10 helpful reviews.', time: '3 hrs ago', icon: 'star', color: 'amber', read: true, date: 'Today' },
            { id: 5, type: 'request', title: 'New request in your program', description: 'A new resource request matching your program has been posted.', time: 'Yesterday', icon: 'document', color: 'seait', read: true, date: 'Yesterday' },
            { id: 6, type: 'lend', title: 'Resource returned successfully', description: 'Physics Lab Manual has been returned by Jane Smith.', time: '2 days ago', icon: 'check', color: 'emerald', read: true, date: 'Earlier' }
        ],
        get filteredNotifications() {
            if (this.activeFilter === 'all') return this.notifications;
            if (this.activeFilter === 'unread') return this.notifications.filter(n => !n.read);
            return this.notifications.filter(n => n.type === this.activeFilter);
        },
        get unreadCount() {
            return this.notifications.filter(n => !n.read).length;
        },
        get groupedNotifications() {
            const groups = {};
            this.filteredNotifications.forEach(n => {
                if (!groups[n.date]) groups[n.date] = [];
                groups[n.date].push(n);
            });
            return groups;
        }
    }">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            <!-- Header Actions -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Notifications</h1>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1" x-text="unreadCount + ' unread'">3 unread</p>
                </div>
                <form method="POST" action="{{ route('notifications.read-all') }}">
                    @csrf
                    <button type="submit" class="btn-secondary text-xs">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Mark all as read
                    </button>
                </form>
            </div>

            <!-- Filter Tabs -->
            <div class="flex items-center gap-1 p-1 bg-gray-100 dark:bg-navy-800/60 rounded-xl w-fit">
                <button @click="activeFilter = 'all'" :class="activeFilter === 'all' ? 'bg-white dark:bg-navy-700 text-gray-900 dark:text-gray-100 shadow-sm' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200'" class="px-4 py-1.5 text-xs font-medium rounded-lg transition-all duration-200">
                    All
                </button>
                <button @click="activeFilter = 'unread'" :class="activeFilter === 'unread' ? 'bg-white dark:bg-navy-700 text-gray-900 dark:text-gray-100 shadow-sm' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200'" class="px-4 py-1.5 text-xs font-medium rounded-lg transition-all duration-200">
                    Unread
                </button>
                <button @click="activeFilter = 'badge'" :class="activeFilter === 'badge' ? 'bg-white dark:bg-navy-700 text-gray-900 dark:text-gray-100 shadow-sm' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200'" class="px-4 py-1.5 text-xs font-medium rounded-lg transition-all duration-200">
                    Badges
                </button>
                <button @click="activeFilter = 'request'" :class="activeFilter === 'request' ? 'bg-white dark:bg-navy-700 text-gray-900 dark:text-gray-100 shadow-sm' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200'" class="px-4 py-1.5 text-xs font-medium rounded-lg transition-all duration-200">
                    Requests
                </button>
                <button @click="activeFilter = 'lend'" :class="activeFilter === 'lend' ? 'bg-white dark:bg-navy-700 text-gray-900 dark:text-gray-100 shadow-sm' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200'" class="px-4 py-1.5 text-xs font-medium rounded-lg transition-all duration-200">
                    Lends
                </button>
            </div>

            <!-- Notifications List -->
            <div class="card p-6">
                <div x-show="filteredNotifications.length === 0" class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                    <h3 class="mt-2 text-sm font-semibold text-gray-900 dark:text-gray-100">No notifications</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">You're all caught up!</p>
                </div>

                <div x-show="filteredNotifications.length > 0" class="space-y-6">
                    <template x-for="(items, date) in groupedNotifications" :key="date">
                        <div>
                            <h3 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-3" x-text="date">Today</h3>
                            <div class="space-y-2">
                                <template x-for="notif in items" :key="notif.id">
                                    <div class="flex items-start gap-4 p-4 rounded-xl transition-all duration-200 hover:bg-gray-50 dark:hover:bg-navy-700/30 cursor-pointer" :class="!notif.read ? 'bg-seait-50/50 dark:bg-seait-900/10 border border-seait-100 dark:border-seait-800/20' : 'border border-transparent'">
                                        <!-- Icon -->
                                        <div :class="`w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0 ${notif.color === 'amber' ? 'bg-amber-100 text-amber-600 dark:bg-amber-900/30 dark:text-amber-300' : notif.color === 'emerald' ? 'bg-emerald-100 text-emerald-600 dark:bg-emerald-900/30 dark:text-emerald-300' : notif.color === 'blue' ? 'bg-blue-100 text-blue-600 dark:bg-blue-900/30 dark:text-blue-300' : 'bg-seait-100 text-seait-600 dark:bg-seait-900/30 dark:text-seait-300'}`">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-html="
                                                notif.icon === 'star' ? '<path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z\'/>' :
                                                notif.icon === 'check' ? '<path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z\'/>' :
                                                notif.icon === 'clock' ? '<path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z\'/>' :
                                                '<path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M9 12h6m-6 4h6m-11.5-4.5a.5.5 0 011 0v3a.5.5 0 01-1 0v-3zM5 12a7 7 0 1114 0 7 7 0 01-14 0z\'/>'
                                            "></svg>
                                        </div>
                                        <!-- Content -->
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-start justify-between gap-2">
                                                <div>
                                                    <p class="text-sm font-semibold text-gray-900 dark:text-gray-100" x-text="notif.title"></p>
                                                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-0.5" x-text="notif.description"></p>
                                                </div>
                                                <span class="text-xs text-gray-400 dark:text-gray-500 flex-shrink-0" x-text="notif.time"></span>
                                            </div>
                                        </div>
                                        <!-- Unread dot -->
                                        <div x-show="!notif.read" class="w-2.5 h-2.5 bg-seait-500 rounded-full flex-shrink-0 mt-1.5 ring-2 ring-seait-100 dark:ring-seait-900/20"></div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
