@props([
    'label' => 'Stat',
    'value' => '—',
    'icon'  => 'chart-bar',
    'tone'  => 'default',   {{-- default | alert | warning | good --}}
    'sub'   => null,
])

<div class="admin-stat-card">
    <div class="admin-stat-icon">
        <x-icon :name="$icon" class="w-4 h-4" />
    </div>
    <div class="min-w-0">
        <p class="admin-stat-label">{{ $label }}</p>
        <p class="admin-stat-value {{ $tone !== 'default' ? $tone : '' }}">
            {{ $value }}
        </p>
        @if ($sub)
            <p class="admin-stat-sub">{{ $sub }}</p>
        @endif
    </div>
</div>