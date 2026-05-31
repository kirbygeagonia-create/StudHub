<div class="sidebar-section-label">SAO</div>

<a href="{{ route('sao.dashboard') }}"
   class="sidebar-link {{ request()->routeIs('sao.dashboard') ? 'active' : '' }}">
    <x-icon name="home" class="w-4 h-4 flex-shrink-0" />
    Dashboard
</a>

<a href="{{ route('sao.feedback') }}"
   class="sidebar-link {{ request()->routeIs('sao.feedback*') ? 'active' : '' }}">
    <x-icon name="feedback" class="w-4 h-4 flex-shrink-0" />
    All Feedback
    @if ($unreadFeedback > 0)
        <span class="sidebar-badge {{ $unreadFeedback > 5 ? 'urgent' : '' }}">
            {{ $unreadFeedback }}
        </span>
    @endif
</a>

<a href="{{ route('moderation.dashboard') }}"
   class="sidebar-link {{ request()->routeIs('moderation.*') ? 'active' : '' }}">
    <x-icon name="flag" class="w-4 h-4 flex-shrink-0" />
    Reports
    @if ($openReports > 0)
        <span class="sidebar-badge urgent">{{ $openReports }}</span>
    @endif
</a>

<div class="sidebar-divider"></div>
<div class="sidebar-section-label">Administration</div>

<a href="{{ route('sao.users') }}"
   class="sidebar-link {{ request()->routeIs('sao.users*') ? 'active' : '' }}">
    <x-icon name="users" class="w-4 h-4 flex-shrink-0" />
    User Management
</a>

@if (config('studhub.announcements_enabled'))
    <a href="{{ route('sao.announcements') }}"
       class="sidebar-link
              {{ request()->routeIs('sao.announcements*') ? 'active' : '' }}">
        <x-icon name="megaphone" class="w-4 h-4 flex-shrink-0" />
        Announcements
    </a>
@endif

<div class="sidebar-divider"></div>

<a href="{{ route('profile.show') }}"
   class="sidebar-link {{ request()->routeIs('profile.*') ? 'active' : '' }}">
    <x-icon name="profile" class="w-4 h-4 flex-shrink-0" />
    Profile
</a>