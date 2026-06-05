<!DOCTYPE html>
<html lang="en" class="">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>StudHub — Component Preview</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css'])
    @livewireStyles
    <style>
        .preview-section { border: 1px dashed #d1d5db; border-radius: 12px; padding: 1.5rem; margin-bottom: 2rem; }
        .preview-section h2 { font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #6b7280; margin-bottom: 1rem; }
        .preview-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 1rem; }
        .preview-item { display: flex; flex-direction: column; align-items: center; gap: 0.5rem; padding: 1rem; background: #f9fafb; border-radius: 8px; }
        .preview-item span { font-size: 0.75rem; color: #6b7280; }
        .dark .preview-section { border-color: #374151; }
        .dark .preview-item { background: #1e293b; }
        .dark .preview-item span { color: #94a3b8; }
    </style>
</head>
<body class="font-sans antialiased bg-gray-50 dark:bg-navy-900 text-gray-900 dark:text-gray-100 p-8">
    <div class="max-w-6xl mx-auto">
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-2xl font-bold">StudHub Components</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Preview all Blade components with their prop variants</p>
            </div>
            <button onclick="document.documentElement.classList.toggle('dark')"
                    class="px-4 py-2 rounded-xl border border-gray-200 dark:border-navy-700 text-sm font-medium hover:bg-gray-100 dark:hover:bg-navy-800 transition-colors">
                Toggle Dark Mode
            </button>
        </div>

        {{-- ═══ Icons ═══ --}}
        <div class="preview-section">
            <h2>Icons — <code>&lt;x-icon name="..." /&gt;</code></h2>
            <div class="preview-grid">
                @foreach (['home','chat','resources','leaderboard','lends','moderation','admin','search','profile','shelf','notifications','feedback','dark','light','logout','plus','check','warning','chevron-down','upload','attachment','send','spinner','book','bolt','sparkle','building','thumbs-up','star','star-outline','document','empty-chat','empty-search','empty-lend-out','empty-lend-in','settings','flag','users','college','chart-bar','shield','megaphone'] as $iconName)
                    <div class="preview-item">
                        <x-icon :name="$iconName" class="w-6 h-6 text-seait-500" />
                        <span>{{ $iconName }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- ═══ Buttons ═══ --}}
        <div class="preview-section">
            <h2>Buttons</h2>
            <div class="flex flex-wrap gap-3 items-center">
                <x-primary-button>Primary</x-primary-button>
                <x-secondary-button>Secondary</x-secondary-button>
                <x-danger-button>Danger</x-danger-button>
                <a href="#" class="btn-primary text-xs">btn-primary xs</a>
                <a href="#" class="btn-secondary text-xs">btn-secondary xs</a>
                <a href="#" class="btn-ghost text-xs">btn-ghost xs</a>
                <a href="#" class="btn-gradient text-xs">btn-gradient xs</a>
            </div>
        </div>

        {{-- ═══ Admin Stat Card ═══ --}}
        <div class="preview-section">
            <h2>Admin Stat Card — <code>&lt;x-admin-stat-card /&gt;</code></h2>
            <div class="grid grid-cols-4 gap-3">
                <x-admin-stat-card label="Students" value="1,234" icon="users" />
                <x-admin-stat-card label="Reports" value="5" icon="flag" tone="alert" sub="needs attention" />
                <x-admin-stat-card label="Moderators" value="0" icon="moderation" tone="warning" />
                <x-admin-stat-card label="Resources" value="456" icon="resources" tone="good" sub="all programs" />
            </div>
        </div>

        {{-- ═══ Empty State ═══ --}}
        <div class="preview-section">
            <h2>Empty State — <code>&lt;x-empty-state /&gt;</code></h2>
            <div class="grid grid-cols-2 gap-4">
                <x-empty-state icon="document" title="No resources yet" description="Upload your first resource to get started." />
                <x-empty-state icon="empty-chat" title="No messages" description="Be the first to say something!" actionLabel="Start chatting" actionUrl="#" />
            </div>
        </div>

        {{-- ═══ Page Header ═══ --}}
        <div class="preview-section">
            <h2>Page Header — <code>&lt;x-page-header /&gt;</code></h2>
            <div class="bg-white dark:bg-navy-800 rounded-xl border border-gray-200 dark:border-navy-700 p-4">
                <div class="flex items-center justify-between">
                    <h2 class="text-2xl font-bold text-gray-900 tracking-tight dark:text-gray-100">Sample Page Title</h2>
                    <div class="flex items-center gap-3">
                        <a href="#" class="btn-secondary text-xs">Secondary</a>
                        <a href="#" class="btn-primary text-xs">Primary</a>
                    </div>
                </div>
            </div>
        </div>

        {{-- ═══ Form Elements ═══ --}}
        <div class="preview-section">
            <h2>Form Elements</h2>
            <div class="grid grid-cols-2 gap-6 max-w-lg">
                <div>
                    <x-input-label for="demo-text" value="Text Input" />
                    <x-text-input id="demo-text" class="block mt-1 w-full" type="text" placeholder="Enter something…" />
                    <x-input-error :messages="['Sample error message']" class="mt-1" />
                </div>
                <div>
                    <x-input-label for="demo-select" value="Select" />
                    <select id="demo-select" class="input-field w-full mt-1">
                        <option>Option 1</option>
                        <option>Option 2</option>
                        <option>Option 3</option>
                    </select>
                </div>
                <div>
                    <x-input-label value="Checkbox" />
                    <label class="inline-flex items-center gap-2 mt-1 text-sm">
                        <input type="checkbox" class="rounded border-gray-300 dark:border-navy-600"> Remember me
                    </label>
                </div>
                <div>
                    <x-input-label value="Date" />
                    <input type="date" class="input-field w-full mt-1">
                </div>
            </div>
        </div>

        {{-- ═══ Dropdown ═══ --}}
        <div class="preview-section">
            <h2>Dropdown — <code>&lt;x-dropdown /&gt;</code></h2>
            <div class="relative inline-block">
                <x-dropdown align="left" width="56">
                    <x-slot name="trigger">
                        <button class="btn-primary text-xs">Open Dropdown</button>
                    </x-slot>
                    <x-slot name="content">
                        <x-dropdown-link href="#">Profile</x-dropdown-link>
                        <x-dropdown-link href="#">Settings</x-dropdown-link>
                        <div class="border-t border-gray-100 dark:border-navy-700/50"></div>
                        <x-dropdown-link href="#">Log Out</x-dropdown-link>
                    </x-slot>
                </x-dropdown>
            </div>
        </div>

        {{-- ═══ Modal ═══ --}}
        <div class="preview-section">
            <h2>Modal — <code>&lt;x-modal /&gt;</code></h2>
            <div x-data="{ showModal: false }">
                <button @click="showModal = true" class="btn-primary text-xs">Open Modal</button>
                <x-modal name="demo-modal" :show="showModal" @close="showModal = false">
                    <div class="p-6">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100">Modal Title</h3>
                        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">This is a sample modal dialog with some content.</p>
                        <div class="mt-6 flex justify-end gap-3">
                            <button @click="showModal = false" class="btn-secondary text-xs">Cancel</button>
                            <button @click="showModal = false" class="btn-primary text-xs">Confirm</button>
                        </div>
                    </div>
                </x-modal>
            </div>
        </div>

        {{-- ═══ Navigation Links ═══ --}}
        <div class="preview-section">
            <h2>Navigation Links</h2>
            <div class="flex flex-wrap gap-2">
                <x-nav-link :href="'#'" :active="true">Active</x-nav-link>
                <x-nav-link :href="'#'">Inactive</x-nav-link>
                <x-responsive-nav-link :href="'#'" :active="true">Mobile Active</x-responsive-nav-link>
                <x-responsive-nav-link :href="'#'">Mobile Inactive</x-responsive-nav-link>
            </div>
        </div>

        {{-- ═══ Role Context Banner ═══ --}}
        <div class="preview-section">
            <h2>Role Context Banner — <code>&lt;x-role-context-banner /&gt;</code></h2>
            <x-role-context-banner />
        </div>

        {{-- ═══ Application Logo ═══ --}}
        <div class="preview-section">
            <h2>Application Logo — <code>&lt;x-application-logo /&gt;</code></h2>
            <div class="flex items-center gap-4">
                <x-application-logo class="w-10 h-10" />
                <x-application-logo class="w-16 h-16" />
            </div>
        </div>

        {{-- ═══ Auth Session Status ═══ --}}
        <div class="preview-section">
            <h2>Auth Session Status — <code>&lt;x-auth-session-status /&gt;</code></h2>
            <x-auth-session-status class="mb-4" :status="'You are logged in!'" />
        </div>

        <div class="mt-12 text-center text-xs text-gray-400 dark:text-gray-500">
            StudHub Component Preview — {{ now()->format('Y-m-d H:i') }}
        </div>
    </div>

    @livewireScripts
</body>
</html>