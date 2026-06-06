<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class NotificationController extends Controller
{
    /**
     * Display all notifications for the current user.
     */
    public function index(): View
    {
        $user = Auth::user();
        assert($user !== null);

        /** @var LengthAwarePaginator<DatabaseNotification> $notifications */
        $notifications = $user
            ->notifications()
            ->latest()
            ->paginate(50);

        $unreadCount = $user->unreadNotifications->count(); // @phpstan-ignore-line — magic relation from Notifiable trait

        // Group notifications by type for collapsible sections
        /** @var LengthAwarePaginator<DatabaseNotification> $notifications */
        $grouped = collect($notifications->items())->groupBy(function (DatabaseNotification $notification): string {
            $data = $notification->data;

            return $data['type'] ?? 'other';
        })->sortKeys();

        return view('notifications.index', compact('notifications', 'unreadCount', 'grouped'));
    }

    /**
     * Fetch recent notifications as JSON for the dropdown.
     */
    public function fetch(): JsonResponse
    {
        $user = Auth::user();
        assert($user !== null);

        $notifications = $user->notifications()
            ->take(10)
            ->get()
            ->map(function (DatabaseNotification $notification) {
                $data = $notification->data;
                $type = $data['type'] ?? 'info';

                return [
                    'id' => $notification->id,
                    'type' => $type,
                    'title' => $data['badge_label'] ?? $data['title'] ?? $data['message'] ?? 'Notification',
                    'time' => $notification->created_at?->diffForHumans(),
                    'read' => $notification->read_at !== null,
                    'link' => $this->notificationLink($type, $data),
                    'icon' => $this->notificationIcon($type),
                    'color' => $this->notificationColor($type),
                ];
            });

        return response()->json([
            'notifications' => $notifications,
            'unread_count' => $user->unreadNotifications->count(), // @phpstan-ignore-line — magic relation from Notifiable trait
        ]);
    }

    /**
     * Mark a single notification as read.
     */
    public function markAsRead(DatabaseNotification $notification): JsonResponse
    {
        if ($notification->notifiable_id !== Auth::id()) {
            abort(403);
        }

        $notification->markAsRead();

        return response()->json(['success' => true]);
    }

    /**
     * Mark all unread notifications as read.
     */
    public function markAllAsRead(): RedirectResponse
    {
        Auth::user()->unreadNotifications->markAsRead(); // @phpstan-ignore-line — magic relation from Notifiable trait

        return redirect()->route('notifications.index')
            ->with('status', 'All notifications marked as read.');
    }

    /**
     * Determine the link for a notification based on its type.
     *
     * @param  array<string, mixed>  $data
     */
    private function notificationLink(string $type, array $data): string
    {
        return match ($type) {
            'badge_earned' => route('profile.show'),
            'chat.mention' => route('chat.index'),
            'request_routed' => route('resources.index'),
            'return_reminder' => route('lends.index'),
            default => route('notifications.index'),
        };
    }

    /**
     * Determine the icon for a notification based on its type.
     */
    private function notificationIcon(string $type): string
    {
        return match ($type) {
            'badge_earned' => 'star',
            'chat.mention' => 'chat',
            'request_routed' => 'check',
            'return_reminder' => 'clock',
            default => 'bell',
        };
    }

    /**
     * Determine the color for a notification based on its type.
     */
    private function notificationColor(string $type): string
    {
        return match ($type) {
            'badge_earned' => 'amber',
            'chat.mention' => 'blue',
            'request_routed' => 'emerald',
            'return_reminder' => 'purple',
            default => 'gray',
        };
    }
}
