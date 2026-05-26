<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-900 dark:text-gray-100">Notifications</h2>
    </x-slot>

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
                        <button type="submit" class="btn-secondary text-xs">
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
                    <div class="space-y-1">
                        @foreach ($notifications as $notification)
                            @php
                                $data = $notification->data;
                                $type = $data['type'] ?? 'info';
                                $title = $data['badge_label'] ?? $data['title'] ?? $data['message'] ?? 'Notification';
                                $description = $data['badge_desc'] ?? $data['message'] ?? '';
                                $icon = $data['badge_icon'] ?? '';
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
                               class="flex items-start gap-4 p-4 rounded-xl transition-all duration-200 hover:bg-gray-50 dark:hover:bg-navy-700/30 {{ $isUnread ? 'bg-seait-50/50 dark:bg-seait-900/10 border border-seait-100 dark:border-seait-800/20' : 'border border-transparent' }}">
                                <!-- Icon -->
                                <div class="w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0 {{ $colorClass }}">
                                    @if ($icon)
                                        <span class="text-lg">{{ $icon }}</span>
                                    @else
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                                    @endif
                                </div>
                                <!-- Content -->
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
                                <!-- Unread dot -->
                                @if ($isUnread)
                                    <div class="w-2.5 h-2.5 bg-seait-500 rounded-full flex-shrink-0 mt-1.5 ring-2 ring-seait-100 dark:ring-seait-900/20"></div>
                                @endif
                            </a>
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