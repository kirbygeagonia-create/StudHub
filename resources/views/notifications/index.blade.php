<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-900 dark:text-gray-100">Notifications</h2>
    </x-slot>

    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 mt-2">
        <a href="{{ route('profile.show') }}" class="inline-flex items-center gap-1 text-xs text-gray-400 hover:text-seait-500 dark:hover:text-seait-400 transition-colors">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Back to Profile
        </a>
    </div>
    <div class="py-8">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            <!-- Header Actions -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Notifications</h1>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ $unreadCount }} unread</p>
                </div>
                @if ($unreadCount > 0)
                    <form method="POST" action="{{ route('notifications.read-all') }}">
                        @csrf
                        <button type="submit" class="btn-secondary text-xs" aria-label="Mark all notifications as read">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Mark all as read
                        </button>
                    </form>
                @endif
            </div>

            <!-- Notifications List -->
            <div class="card p-6">
                @if ($notifications->isEmpty())
                    <div class="text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                        <h3 class="mt-2 text-sm font-semibold text-gray-900 dark:text-gray-100">No notifications</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">You're all caught up!</p>
                    </div>
                @else
                    @php
                        $typeLabels = [
                            'badge_earned' => ['label' => 'Badges Earned', 'icon' => 'star', 'color' => 'amber'],
                            'chat.mention' => ['label' => 'Chat Mentions', 'icon' => 'chat', 'color' => 'blue'],
                            'request_routed' => ['label' => 'Request Updates', 'icon' => 'check', 'color' => 'emerald'],
                            'return_reminder' => ['label' => 'Return Reminders', 'icon' => 'clock', 'color' => 'purple'],
                            'other' => ['label' => 'Other', 'icon' => 'bell', 'color' => 'gray'],
                        ];
                    @endphp

                    <div x-data="{ openGroups: {} }" class="space-y-4">
                        @foreach ($grouped as $type => $typeNotifications)
                            @php
                                $meta = $typeLabels[$type] ?? $typeLabels['other'];
                                $groupId = 'group-' . Str::slug($type);
                                $groupUnread = $typeNotifications->filter(fn ($n) => $n->read_at === null)->count();
                            @endphp
                            <div class="border border-gray-100 dark:border-navy-700/50 rounded-xl overflow-hidden">
                                <button type="button"
                                        @@click="openGroups['{{ $groupId }}'] = !openGroups['{{ $groupId }}']"
                                        class="w-full flex items-center justify-between px-4 py-3 bg-gray-50/50 dark:bg-navy-800/50 hover:bg-gray-100 dark:hover:bg-navy-700/50 transition-colors text-left"
                                        aria-label="Toggle {{ $meta['label'] }} section">
                                    <div class="flex items-center gap-2">
                                        <span class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $meta['label'] }}</span>
                                        <span class="text-xs text-gray-400 dark:text-gray-500">({{ $typeNotifications->count() }})</span>
                                        @if ($groupUnread > 0)
                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold bg-seait-100 text-seait-700 dark:bg-seait-900/30 dark:text-seait-300">{{ $groupUnread }} new</span>
                                        @endif
                                    </div>
                                    <svg class="w-4 h-4 text-gray-400 transition-transform duration-200"
                                         :class="{ 'rotate-180': openGroups['{{ $groupId }}'] }"
                                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </button>
                                <div x-show="openGroups['{{ $groupId }}']" x-cloak class="divide-y divide-gray-50 dark:divide-navy-700/30">
                                    @foreach ($typeNotifications as $notification)
                                        @php
                                            $data = $notification->data;
                                            $title = $data['badge_label'] ?? $data['title'] ?? $data['message'] ?? 'Notification';
                                            $description = $data['badge_desc'] ?? $data['message'] ?? '';
                                            $link = match ($type) {
                                                'badge_earned' => route('profile.show'),
                                                'chat.mention' => route('chat.index'),
                                                'request_routed' => route('resources.index'),
                                                'return_reminder' => route('lends.index'),
                                                default => route('notifications.index'),
                                            };
                                            $isUnread = $notification->read_at === null;
                                            $colorClass = match ($type) {
                                                'badge_earned' => 'bg-amber-100 text-amber-600 dark:bg-amber-900/30 dark:text-amber-300',
                                                'chat.mention' => 'bg-blue-100 text-blue-600 dark:bg-blue-900/30 dark:text-blue-300',
                                                'request_routed' => 'bg-emerald-100 text-emerald-600 dark:bg-emerald-900/30 dark:text-emerald-300',
                                                'return_reminder' => 'bg-purple-100 text-purple-600 dark:bg-purple-900/30 dark:text-purple-300',
                                                default => 'bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400',
                                            };
                                        @endphp
                                        <a href="{{ $link }}"
                                           onclick="event.preventDefault(); fetch('{{ route('notifications.read', $notification) }}', { method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'X-Requested-With': 'XMLHttpRequest' } }).then(() => { window.location.href = '{{ $link }}'; });"
                                           class="flex items-start gap-4 p-4 transition-all duration-200 hover:bg-gray-50 dark:hover:bg-navy-700/30 {{ $isUnread ? 'bg-seait-50/50 dark:bg-seait-900/10' : '' }}">
                                            <div class="w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0 {{ $colorClass }}">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <div class="flex items-start justify-between gap-2">
                                                    <div>
                                                        <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $title }}</p>
                                                        @if ($description)
                                                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-0.5">{{ $description }}</p>
                                                        @endif
                                                    </div>
                                                    <span class="text-xs text-gray-400 dark:text-gray-500 flex-shrink-0 whitespace-nowrap">{{ $notification->created_at->diffForHumans() }}</span>
                                                </div>
                                            </div>
                                            @if ($isUnread)
                                                <div class="w-2.5 h-2.5 bg-seait-500 rounded-full flex-shrink-0 mt-1.5 ring-2 ring-seait-100 dark:ring-seait-900/20"></div>
                                            @endif
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-6">
                        {{ $notifications->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>