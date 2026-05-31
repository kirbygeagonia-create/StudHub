<div class="sidebar-section-label">Dean</div>

<a href="{{ route('dean.dashboard') }}"
   class="sidebar-link {{ request()->routeIs('dean.dashboard') ? 'active' : '' }}">
    <x-icon name="home" class="w-4 h-4 flex-shrink-0" />
    Dashboard
</a>

<a href="{{ route('dean.feedback') }}"
   class="sidebar-link {{ request()->routeIs('dean.feedback*') ? 'active' : '' }}">
    <x-icon name="feedback" class="w-4 h-4 flex-shrink-0" />
    Feedback
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
<div class="sidebar-section-label">College</div>

<a href="{{ route('dean.programs') }}"
   class="sidebar-link {{ request()->routeIs('dean.programs*') ? 'active' : '' }}">
    <x-icon name="college" class="w-4 h-4 flex-shrink-0" />
    Programs
</a>

<a href="{{ route('resources.index') }}"
   class="sidebar-link {{ request()->routeIs('resources.*') ? 'active' : '' }}">
    <x-icon name="resources" class="w-4 h-4 flex-shrink-0" />
    Resources
</a>

<a href="{{ route('leaderboard') }}"
   class="sidebar-link {{ request()->routeIs('leaderboard') ? 'active' : '' }}">
    <x-icon name="leaderboard" class="w-4 h-4 flex-shrink-0" />
    Leaderboard
</a>

<div class="sidebar-divider"></div>

<a href="{{ route('profile.show') }}"
   class="sidebar-link {{ request()->routeIs('profile.*') ? 'active' : '' }}">
    <x-icon name="profile" class="w-4 h-4 flex-shrink-0" />
    Profile
</a>