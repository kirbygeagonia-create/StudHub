# StudHub — Full System Improvements v4
> All file replacements. No build commands needed.

---

## ❓ FIRST: Medals vs Badge Tiers — They Are Different Things

**Badge Tiers** (Seedling → Bookworm → Scribe → … → StudHub Legend) are **permanent achievements** earned when your karma crosses a threshold. They reflect your lifetime contribution to StudHub. Every user always has one.

**Leaderboard Rank (#1, #2, #3)** is a **current standing** — who has the most karma right now among all users in a program. It is not an achievement; it changes every time someone earns more karma.

The 🥇🥈🥉 emoji medals I added in v3 were wrong because they look like achievements and could be confused with the badge system. The fix below replaces them with clean rank numbers (#1 gold-colored, #2 silver, #3 bronze) and ADDS each user's actual Badge Tier label to the leaderboard row — so you can see both "they are #1 on the board" AND "they are a Sage tier". No emoji medals.

---

## FILE 1 — `resources/views/layouts/guest.blade.php`
**Problem:** The logo is called with `fill-current text-gray-500 dark:text-gray-300` — leftover from the old SVG that used `currentColor`. Our new logo uses hardcoded gradients so these classes do nothing except add visual confusion. Also the auth pages need a more welcoming StudHub brand subtitle under the logo.

Find line 97 (the `<x-application-logo>` line):
```html
<x-application-logo class="w-20 h-20 fill-current text-gray-500 dark:text-gray-300" />
```

Replace with:
```html
<x-application-logo class="w-20 h-20" />
<p class="mt-2 text-xs font-semibold text-seait-500 dark:text-seait-400 tracking-widest uppercase">StudHub</p>
<p class="text-[10px] text-gray-400 dark:text-gray-500">SEAIT Academic Resource Exchange</p>
```

---

## FILE 2 — `resources/views/welcome.blade.php` (nav logo only)
**Problem:** The welcome page nav still shows a generic orange square with a photo icon SVG instead of the custom StudHub graduation-cap logo.

Find the nav logo block (lines ~47–52):
```html
<div class="w-8 h-8 bg-gradient-to-br from-seait-400 to-seait-600 rounded-xl flex items-center justify-center shadow-sm group-hover:shadow-md transition-all duration-300">
    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
</div>
```

Replace with the same SVG used in the app nav:
```html
<div class="w-8 h-8 flex items-center justify-center flex-shrink-0">
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 200 200" fill="none" class="w-8 h-8">
        <defs>
            <linearGradient id="wG" x1="0" y1="0" x2="1" y2="1">
                <stop offset="0%" stop-color="#FF6B35"/>
                <stop offset="100%" stop-color="#C94B15"/>
            </linearGradient>
            <linearGradient id="wA" x1="0" y1="1" x2="1" y2="0">
                <stop offset="0%" stop-color="#FFB347"/>
                <stop offset="100%" stop-color="#FF8C5A"/>
            </linearGradient>
        </defs>
        <path d="M100 18 C68 18 38 30 24 52 C10 74 10 100 10 100 C10 132 12 152 30 166 C48 180 72 182 100 182 C128 182 152 180 170 166 C188 152 190 132 190 100 C190 100 190 74 176 52 C162 30 132 18 100 18Z" fill="url(#wG)"/>
        <path d="M100 52 C88 54 66 62 54 74 L54 148 C66 138 88 132 100 132 Z" fill="white" opacity="0.16"/>
        <path d="M100 52 C112 54 134 62 146 74 L146 148 C134 138 112 132 100 132 Z" fill="white" opacity="0.10"/>
        <line x1="100" y1="52" x2="100" y2="148" stroke="white" stroke-width="2" opacity="0.28" stroke-linecap="round"/>
        <rect x="72" y="94" width="56" height="8" rx="4" fill="white" opacity="0.95"/>
        <path d="M100 70 L118 91 L100 99 L82 91 Z" fill="white" opacity="0.95"/>
        <line x1="116" y1="97" x2="116" y2="122" stroke="url(#wA)" stroke-width="3" stroke-linecap="round"/>
        <circle cx="116" cy="126" r="4.5" fill="url(#wA)"/>
    </svg>
</div>
```

Also find the hero section logo (inside the hero CTA area) — it uses the same generic orange square. Apply the same replacement there.

---

## FILE 3 — `resources/views/profile/leaderboard.blade.php`
**Problem:** Medals look like badge tier achievements (confusing). No badge tier shown per user. Visual podium was ok conceptually but wrong implementation.

REPLACE ENTIRE FILE:
```blade
<x-app-layout>
    <x-page-header title="{{ __('Leaderboard') }}" />

    <div class="py-8">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            {{-- Program filter --}}
            <div class="card p-4">
                <form method="GET" action="{{ route('leaderboard') }}" class="flex items-end gap-3">
                    <div class="flex-1">
                        <label for="program_id" class="text-xs font-medium text-gray-700 dark:text-gray-300 mb-1 block">Filter by Program</label>
                        <select id="program_id" name="program_id" class="w-full input-field text-sm" onchange="this.form.submit()">
                            @foreach ($programs as $program)
                                <option value="{{ $program->id }}" @selected($selectedProgramId == $program->id)>
                                    {{ $program->code }} — {{ $program->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </form>
            </div>

            <div class="card overflow-hidden">
                @if ($topSharers->isEmpty())
                    <div class="p-12 text-center">
                        <div class="w-14 h-14 rounded-2xl bg-gray-100 dark:bg-navy-700/50 flex items-center justify-center mx-auto mb-3">
                            <svg class="w-7 h-7 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                        </div>
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100" data-testid="leaderboard-empty">No top sharers yet</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Be the first to earn karma by sharing resources!</p>
                    </div>
                @else
                    {{-- Header --}}
                    <div class="px-6 py-4 bg-gradient-to-r from-seait-50 to-transparent dark:from-seait-900/20 dark:to-transparent border-b border-gray-100 dark:border-navy-700/50">
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            Rankings show total karma earned. Your
                            <span class="font-semibold text-gray-700 dark:text-gray-300">Badge Tier</span>
                            (e.g. Scholar, Luminary) is earned separately based on karma milestones — it does not reset.
                        </p>
                    </div>

                    <ol class="divide-y divide-gray-100 dark:divide-navy-700/50" data-testid="leaderboard-list">
                        @foreach ($topSharers as $index => $sharer)
                            @php
                                $isMe = auth()->id() === $sharer->id;
                                $badge = \App\Domain\Reputation\Enums\BadgeTier::fromKarma($sharer->karma ?? 0);
                                $rankColors = [
                                    0 => 'bg-amber-400 text-white',
                                    1 => 'bg-gray-400 text-white',
                                    2 => 'bg-amber-700 text-white',
                                ];
                                $rankColor = $rankColors[$index] ?? 'bg-gray-100 text-gray-500 dark:bg-navy-700 dark:text-gray-400';
                            @endphp
                            <li data-testid="leaderboard-item"
                                class="flex items-center gap-4 px-5 py-4 {{ $isMe ? 'bg-seait-50/60 dark:bg-seait-900/10' : 'hover:bg-gray-50/80 dark:hover:bg-navy-700/30' }} transition-colors">

                                {{-- Rank badge --}}
                                <div class="w-9 h-9 rounded-xl {{ $rankColor }} flex items-center justify-center font-bold text-sm flex-shrink-0 shadow-sm">
                                    {{ $index + 1 }}
                                </div>

                                {{-- Avatar --}}
                                <div class="w-9 h-9 rounded-full bg-gradient-to-br from-seait-400 to-seait-600 flex items-center justify-center text-white text-xs font-bold flex-shrink-0 shadow-sm">
                                    {{ strtoupper(substr($sharer->display_name ?: $sharer->name, 0, 2)) }}
                                </div>

                                {{-- Name + badge tier --}}
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <p class="text-sm font-semibold text-gray-900 dark:text-gray-100 truncate">
                                            {{ $sharer->display_name ?: $sharer->name }}
                                        </p>
                                        @if ($isMe)
                                            <span class="text-[10px] font-medium text-seait-500">(you)</span>
                                        @endif
                                    </div>
                                    <div class="flex items-center gap-2 mt-0.5">
                                        {{-- Actual badge tier — different from leaderboard rank --}}
                                        <span class="inline-flex items-center gap-1 text-[10px] font-semibold px-1.5 py-0.5 rounded-md
                                            {{ in_array($badge->rarity()->value, ['legendary', 'rare']) ? 'bg-amber-50 text-amber-700 dark:bg-amber-900/20 dark:text-amber-300' : 'bg-gray-100 text-gray-600 dark:bg-navy-700 dark:text-gray-400' }}">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                {!! $badge->icon() !!}
                                            </svg>
                                            {{ $badge->label() }}
                                        </span>
                                        @if ($sharer->year_level)
                                            <span class="text-[10px] text-gray-400 dark:text-gray-500">Year {{ $sharer->year_level }}</span>
                                        @endif
                                    </div>
                                </div>

                                {{-- Karma score --}}
                                <div class="text-right flex-shrink-0">
                                    <p class="text-base font-bold text-gray-900 dark:text-gray-100" data-testid="leaderboard-karma">
                                        {{ number_format($sharer->karma) }}
                                    </p>
                                    <p class="text-[10px] text-gray-400 dark:text-gray-500">karma</p>
                                </div>
                            </li>
                        @endforeach
                    </ol>
                @endif
            </div>

            {{-- What is karma? --}}
            <div class="card p-5 border-l-4 border-seait-400 bg-seait-50/30 dark:bg-seait-900/10">
                <p class="text-sm font-semibold text-gray-900 dark:text-gray-100 mb-1">How rankings and badges work</p>
                <p class="text-xs text-gray-600 dark:text-gray-400 leading-relaxed">
                    <strong class="text-gray-800 dark:text-gray-200">Leaderboard rank</strong> (the numbered position) shows who has earned the most karma in your program.
                    It changes as others earn or spend karma.<br>
                    <strong class="text-gray-800 dark:text-gray-200">Badge Tiers</strong> (Seedling → StudHub Legend) are permanent milestones you unlock by reaching karma thresholds — they never go down.
                    Earn karma by uploading resources, fulfilling requests, and lending materials.
                </p>
            </div>

        </div>
    </div>
</x-app-layout>
```

---

## FILE 4 — `resources/views/requests/index.blade.php`
**Problems:**
- Request items are plain `<li>` rows — no visual weight or urgency colour coding
- Filter button uses `!bg-gray-700` override hack — should use `btn-secondary`
- Urgency badge is on the right but the item title is just a link with no context icon
- Missing count of open requests

REPLACE ENTIRE FILE:
```blade
<x-app-layout>
    <x-page-header title="{{ __('Request Board') }}">
        <x-slot name="actions">
            <a href="{{ route('requests.create') }}" class="btn-primary text-xs flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                New request
            </a>
        </x-slot>
    </x-page-header>

    <div class="py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            {{-- Filters --}}
            <div class="card p-4">
                <form method="GET" action="{{ route('requests.index') }}" class="grid grid-cols-2 md:grid-cols-5 gap-3 items-end">
                    <div class="col-span-2 md:col-span-1">
                        <label for="subject_id" class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Subject</label>
                        <select id="subject_id" name="subject_id" class="w-full input-field text-sm">
                            <option value="">All subjects</option>
                            @foreach ($subjects as $subject)
                                <option value="{{ $subject->id }}" @selected(($filters['subject_id'] ?? '') == $subject->id)>{{ $subject->code }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="type_wanted" class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Type wanted</label>
                        <select id="type_wanted" name="type_wanted" class="w-full input-field text-sm">
                            <option value="">All types</option>
                            @foreach ($types as $type)
                                <option value="{{ $type->value }}" @selected(($filters['type_wanted'] ?? '') == $type->value)>{{ $type->label() }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="urgency" class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Urgency</label>
                        <select id="urgency" name="urgency" class="w-full input-field text-sm">
                            <option value="">All</option>
                            @foreach ($urgencies as $urgency)
                                <option value="{{ $urgency->value }}" @selected(($filters['urgency'] ?? '') == $urgency->value)>{{ $urgency->label() }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-span-2 md:col-span-1 flex gap-2 items-end">
                        <button type="submit" class="flex-1 btn-secondary text-xs">Filter</button>
                        <a href="{{ route('requests.index') }}" class="flex-1 btn-ghost text-xs text-center">Clear</a>
                    </div>
                </form>
            </div>

            {{-- List --}}
            <div class="space-y-3">
                @if ($requests->isEmpty())
                    <div class="card p-12 text-center">
                        <div class="w-14 h-14 rounded-2xl bg-gray-100 dark:bg-navy-700/50 flex items-center justify-center mx-auto mb-3">
                            <svg class="w-7 h-7 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/></svg>
                        </div>
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">No open requests</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Need a reviewer or textbook? Post a request — it'll be routed to programs that teach the same subject.</p>
                        <a href="{{ route('requests.create') }}" class="mt-4 inline-block btn-primary text-xs">Post a request</a>
                    </div>
                @else
                    <div x-data="{ loaded: false }" x-init="setTimeout(() => loaded = true, 300)">
                        {{-- Skeletons --}}
                        <div x-show="!loaded" class="space-y-3">
                            @for ($i = 0; $i < 4; $i++)
                                <div class="card p-5 flex items-start gap-4">
                                    <div class="skeleton w-10 h-10 rounded-xl flex-shrink-0"></div>
                                    <div class="flex-1 space-y-2">
                                        <div class="skeleton h-4 w-2/3"></div>
                                        <div class="skeleton h-3 w-1/2"></div>
                                    </div>
                                </div>
                            @endfor
                        </div>

                        {{-- Actual cards --}}
                        <div x-show="loaded" class="space-y-3" data-testid="requests-list">
                            @foreach ($requests as $request)
                                @php
                                    $urgencyStyle = match ($request->urgency?->value) {
                                        'urgent' => 'border-l-red-400 bg-red-50/30 dark:bg-red-900/5',
                                        'low'    => 'border-l-gray-300 dark:border-l-navy-600',
                                        default  => 'border-l-amber-400',
                                    };
                                    $badgeStyle = match ($request->urgency?->value) {
                                        'urgent' => 'bg-red-50 text-red-700 dark:bg-red-900/20 dark:text-red-300',
                                        'low'    => 'bg-gray-100 text-gray-600 dark:bg-navy-700 dark:text-gray-400',
                                        default  => 'bg-amber-50 text-amber-700 dark:bg-amber-900/20 dark:text-amber-300',
                                    };
                                @endphp
                                <a href="{{ route('requests.show', $request) }}"
                                   class="card card-hover p-5 flex items-start gap-4 border-l-4 {{ $urgencyStyle }} block"
                                   data-testid="request-item">
                                    {{-- Icon --}}
                                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-100 to-blue-200 dark:from-blue-900/30 dark:to-blue-800/20 flex items-center justify-center flex-shrink-0 mt-0.5">
                                        <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-start justify-between gap-3">
                                            <p class="text-sm font-semibold text-gray-900 dark:text-gray-100 truncate">
                                                {{ $request->subject->code }} — {{ $request->type_wanted }}
                                            </p>
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium flex-shrink-0 {{ $badgeStyle }}">
                                                {{ $request->urgency?->label() ?: 'Normal' }}
                                            </span>
                                        </div>
                                        @if ($request->description)
                                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 line-clamp-2">{{ $request->description }}</p>
                                        @endif
                                        <div class="flex items-center gap-3 mt-2 text-[11px] text-gray-400 dark:text-gray-500">
                                            <span>by {{ $request->requester?->display_name ?: $request->requester?->name ?: 'Unknown' }}</span>
                                            @if ($request->needed_by)
                                                <span>· Needed by {{ $request->needed_by->format('M d') }}</span>
                                            @endif
                                            <span>· {{ $request->created_at->diffForHumans() }}</span>
                                            <span>· {{ $request->offers->count() }} {{ Str::plural('offer', $request->offers->count()) }}</span>
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                    <div class="mt-4">{{ $requests->links() }}</div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
```

---

## FILE 5 — `resources/views/requests/show.blade.php`
**Problem:** Plain DL grid with no visual anchor, offers list is bare.

Replace the opening card section — find:
```blade
<div class="card p-6 space-y-3">
    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
```

Replace with:
```blade
<div class="card overflow-hidden">
    {{-- Header banner --}}
    <div class="px-6 py-5 border-b border-gray-100 dark:border-navy-700/50">
        <div class="flex items-start gap-4">
            @php
                $urgBg = match ($request->urgency?->value) {
                    'urgent' => 'from-red-400 to-red-600',
                    'low'    => 'from-gray-400 to-gray-500',
                    default  => 'from-amber-400 to-amber-600',
                };
                $urgBadge = match ($request->urgency?->value) {
                    'urgent' => 'bg-red-50 text-red-700 dark:bg-red-900/20 dark:text-red-300',
                    'low'    => 'bg-gray-100 text-gray-600 dark:bg-navy-700 dark:text-gray-400',
                    default  => 'bg-amber-50 text-amber-700 dark:bg-amber-900/20 dark:text-amber-300',
                };
            @endphp
            <div class="w-12 h-12 rounded-2xl bg-gradient-to-br {{ $urgBg }} flex items-center justify-center flex-shrink-0 shadow-sm">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>
            <div class="flex-1">
                <h1 class="text-xl font-bold text-gray-900 dark:text-gray-100">
                    {{ $request->subject->code }} — {{ $request->type_wanted }}
                </h1>
                <div class="flex items-center gap-2 mt-1">
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $urgBadge }}">
                        {{ $request->urgency?->label() ?? 'Normal' }} urgency
                    </span>
                    <span class="text-xs text-gray-400 dark:text-gray-500">{{ $request->created_at->diffForHumans() }}</span>
                </div>
            </div>
        </div>
    </div>
    <div class="p-6 space-y-3">
    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
```

And add one closing `</div>` after the final `</dl>` to close the new inner div.

---

## FILE 6 — `resources/views/resources/shelf.blade.php`
**Problem:** Plain `<li>` list with a bare "Remove" text link. Doesn't match the card layout in `resources/index`.

REPLACE ENTIRE FILE:
```blade
<x-app-layout>
    <x-page-header title="{{ __('My Shelf') }}">
        <x-slot name="actions">
            <a href="{{ route('resources.index') }}" class="btn-secondary text-xs">Browse resources</a>
        </x-slot>
    </x-page-header>

    <div class="py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

            @if ($shelf && $resources->isNotEmpty())
                <div class="space-y-3">
                    <p class="text-xs text-gray-500 dark:text-gray-400 px-1">{{ $resources->total() }} saved {{ Str::plural('resource', $resources->total()) }}</p>

                    @foreach ($resources as $resource)
                        <div class="card card-hover p-5 flex items-start gap-4">
                            {{-- Type icon --}}
                            <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-seait-100 to-seait-200 dark:from-seait-900/30 dark:to-seait-800/20 flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5 text-seait-600 dark:text-seait-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z"/></svg>
                            </div>
                            {{-- Details --}}
                            <div class="flex-1 min-w-0">
                                <a href="{{ route('resources.show', $resource) }}"
                                   class="font-semibold text-gray-900 dark:text-gray-100 hover:text-seait-600 dark:hover:text-seait-400 transition-colors block truncate">
                                    {{ $resource->title }}
                                </a>
                                <div class="flex flex-wrap items-center gap-1.5 mt-1.5">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium bg-gray-100 text-gray-700 dark:bg-navy-700 dark:text-gray-300">{{ $resource->subject->code }}</span>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium bg-seait-50 text-seait-700 dark:bg-seait-900/30 dark:text-seait-300">{{ $resource->type->label() }}</span>
                                    @if ($resource->program)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium bg-gray-100 text-gray-700 dark:bg-navy-700 dark:text-gray-300">{{ $resource->program->code }}</span>
                                    @endif
                                </div>
                                <p class="text-xs text-gray-400 dark:text-gray-500 mt-1.5">Saved {{ $resource->pivot->created_at?->diffForHumans() }}</p>
                            </div>
                            {{-- Remove button --}}
                            <form method="POST" action="{{ route('resources.toggle-save', $resource) }}" class="flex-shrink-0">
                                @csrf
                                <button type="submit"
                                        class="p-2 rounded-xl text-gray-300 hover:text-red-500 hover:bg-red-50 dark:text-gray-600 dark:hover:text-red-400 dark:hover:bg-red-900/10 transition-colors"
                                        title="Remove from shelf">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/></svg>
                                </button>
                            </form>
                        </div>
                    @endforeach

                    <div class="mt-4">{{ $resources->links() }}</div>
                </div>
            @else
                <div class="card p-12 text-center">
                    <div class="w-16 h-16 rounded-2xl bg-gray-100 dark:bg-navy-700/50 flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/></svg>
                    </div>
                    <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100">Your shelf is empty</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Save resources to build your personal study collection.</p>
                    <a href="{{ route('resources.index') }}" class="mt-5 inline-block btn-primary text-xs">Browse resources</a>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>
```

---

## FILE 7 — `resources/views/feedback/create.blade.php`
**Problem:** Uses `<x-primary-button>` (old Laravel component) inconsistent with the rest of the system. No type icons. Feels like a support ticket form, not a community-first feature.

REPLACE ENTIRE FILE:
```blade
<x-app-layout>
    <x-page-header title="{{ __('Send Feedback') }}" />

    <div class="py-8">
        <div class="max-w-xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="card overflow-hidden">
                <div class="px-6 py-5 bg-gradient-to-r from-seait-50 to-transparent dark:from-seait-900/20 dark:to-transparent border-b border-gray-100 dark:border-navy-700/50">
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        Found a bug? Have a feature idea? Your feedback helps shape StudHub for every SEAIT student.
                    </p>
                </div>
                <div class="p-6">
                    <form method="POST" action="{{ route('feedback.store') }}" class="space-y-5">
                        @csrf

                        {{-- Type selector --}}
                        <div>
                            <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">Type</label>
                            <div x-data="{ selected: '{{ old('type', 'feedback') }}'}" class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                                @foreach ([
                                    ['feedback', '💬', 'Feedback'],
                                    ['bug', '🐛', 'Bug Report'],
                                    ['feature', '💡', 'Feature Request'],
                                    ['praise', '🌟', 'Praise'],
                                ] as [$val, $emoji, $label])
                                <label class="cursor-pointer">
                                    <input type="radio" name="type" value="{{ $val }}" class="sr-only" @if(old('type', 'feedback') === $val) checked @endif x-model="selected">
                                    <div :class="selected === '{{ $val }}' ? 'ring-2 ring-seait-400 border-seait-300 bg-seait-50 dark:bg-seait-900/20 dark:border-seait-700' : 'border-gray-200 dark:border-navy-700 hover:border-gray-300 dark:hover:border-navy-600'"
                                         class="flex flex-col items-center gap-1 px-3 py-2.5 rounded-xl border transition-all text-center">
                                        <span class="text-lg leading-none">{{ $emoji }}</span>
                                        <span class="text-xs font-medium text-gray-700 dark:text-gray-300">{{ $label }}</span>
                                    </div>
                                </label>
                                @endforeach
                            </div>
                            @error('type') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                        </div>

                        {{-- Message --}}
                        <div>
                            <label for="body" class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Your message</label>
                            <textarea id="body" name="body" rows="5"
                                      class="input-field w-full"
                                      placeholder="Describe what's on your mind… be as specific as possible."
                                      maxlength="2000"
                                      required>{{ old('body') }}</textarea>
                            @error('body') <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                            <p class="mt-1 text-xs text-gray-400 dark:text-gray-500">5–2,000 characters</p>
                        </div>

                        <div class="flex justify-end">
                            <button type="submit" class="btn-primary">Submit Feedback</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
```

---

## FILE 8 — `resources/views/search/index.blade.php`
**Problem:** Raw `bg-white rounded-lg border` instead of `.card` class. Section headers use `font-semibold text-lg` instead of `section-title`. Results feel like a basic HTML page.

REPLACE ENTIRE FILE:
```blade
<x-app-layout>
    <x-page-header title="{{ __('Search') }}" />

    <div class="py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            {{-- Search bar --}}
            <form method="GET" action="{{ route('search') }}">
                <div class="flex gap-3">
                    <div class="relative flex-1">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </div>
                        <input type="text" name="q" value="{{ $query }}"
                               placeholder="Search resources, requests, messages…"
                               autofocus
                               class="input-field w-full pl-10 text-sm">
                    </div>
                    <button type="submit" class="btn-primary text-sm">Search</button>
                </div>
            </form>

            @if ($query !== '')
                @php $total = $resources->count() + $requests->count() + $messages->count(); @endphp

                @if ($total === 0)
                    <div class="card p-12 text-center">
                        <div class="w-14 h-14 rounded-2xl bg-gray-100 dark:bg-navy-700/50 flex items-center justify-center mx-auto mb-3">
                            <svg class="w-7 h-7 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </div>
                        <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">No results for "{{ $query }}"</p>
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Try different keywords or check your spelling.</p>
                    </div>
                @else
                    <p class="text-xs text-gray-500 dark:text-gray-400 px-1">
                        <span class="font-semibold text-gray-700 dark:text-gray-300">{{ $total }}</span>
                        {{ Str::plural('result', $total) }} for "<span class="font-semibold text-gray-700 dark:text-gray-300">{{ $query }}</span>"
                    </p>

                    @if ($resources->isNotEmpty())
                        <div>
                            <h3 class="section-title mb-3 flex items-center gap-2">
                                <svg class="w-4 h-4 text-seait-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                Resources
                                <span class="text-xs font-medium text-gray-400">({{ $resources->count() }})</span>
                            </h3>
                            <div class="space-y-2">
                                @foreach ($resources as $resource)
                                    <a href="{{ route('resources.show', $resource) }}" class="card card-hover p-4 flex items-center gap-3 block">
                                        <div class="w-9 h-9 rounded-xl bg-seait-100 dark:bg-seait-900/30 flex items-center justify-center flex-shrink-0">
                                            <svg class="w-4 h-4 text-seait-600 dark:text-seait-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-semibold text-gray-900 dark:text-gray-100 truncate">{{ $resource->title }}</p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $resource->subject?->code }} · {{ $resource->type->label() }} · by {{ $resource->owner?->display_name ?? $resource->owner?->name }}</p>
                                        </div>
                                        <svg class="w-4 h-4 text-gray-300 dark:text-gray-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if ($requests->isNotEmpty())
                        <div>
                            <h3 class="section-title mb-3 flex items-center gap-2">
                                <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                Requests
                                <span class="text-xs font-medium text-gray-400">({{ $requests->count() }})</span>
                            </h3>
                            <div class="space-y-2">
                                @foreach ($requests as $request)
                                    <a href="{{ route('requests.show', $request) }}" class="card card-hover p-4 flex items-center gap-3 block">
                                        <div class="w-9 h-9 rounded-xl bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center flex-shrink-0">
                                            <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-semibold text-gray-900 dark:text-gray-100 truncate">{{ $request->subject?->code ?? '' }} — {{ $request->type_wanted }}</p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $request->status->label() }} · by {{ $request->requester?->display_name ?? $request->requester?->name }}</p>
                                        </div>
                                        <svg class="w-4 h-4 text-gray-300 dark:text-gray-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if ($messages->isNotEmpty())
                        <div>
                            <h3 class="section-title mb-3 flex items-center gap-2">
                                <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                                Chat Messages
                                <span class="text-xs font-medium text-gray-400">({{ $messages->count() }})</span>
                            </h3>
                            <div class="space-y-2">
                                @foreach ($messages as $message)
                                    <a href="{{ route('chat.index') }}" class="card card-hover p-4 flex items-start gap-3 block">
                                        <div class="w-9 h-9 rounded-xl bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center flex-shrink-0">
                                            <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm text-gray-700 dark:text-gray-300 line-clamp-2">{{ $message->body }}</p>
                                            <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">{{ $message->sender?->display_name ?? $message->sender?->name }} · {{ $message->created_at->diffForHumans() }}</p>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif
                @endif
            @endif

        </div>
    </div>
</x-app-layout>
```

---

## FILE 9 — `resources/views/profile/show.blade.php`
**Problem:** No karma progress bar showing distance to the next badge tier. The badge section shows current tier but gives no motivation for progression.

Inside the profile card, find the karma/badge display block:
```blade
<div class="text-3xl font-bold text-gray-900 dark:text-gray-100" data-testid="karma-score">{{ $karma }}</div>
<div class="text-xs text-gray-500 dark:text-gray-400">Karma</div>
@if ($badge)
    <span ...>{{ $badge->label() }}</span>
@endif
```

Add a karma progress bar directly after those lines:
```blade
<div class="text-3xl font-bold text-gray-900 dark:text-gray-100" data-testid="karma-score">{{ $karma }}</div>
<div class="text-xs text-gray-500 dark:text-gray-400">Karma</div>
@if ($badge)
    <span class="inline-flex items-center gap-1 mt-1 px-2.5 py-0.5 rounded-full text-xs font-medium
        {{ in_array($badge->rarity()->value, ['legendary', 'rare']) ? 'bg-amber-50 text-amber-700 dark:bg-amber-900/20 dark:text-amber-300' : 'bg-seait-50 text-seait-700 dark:bg-seait-900/20 dark:text-seait-300' }}"
        data-testid="badge-tier">
        {{ $badge->label() }}
    </span>
    @php $nextTier = $badge->next(); $karmaToNext = $nextTier ? $badge->karmaToNext($karma) : 0; @endphp
    @if ($nextTier)
        <div class="mt-2 text-left" style="min-width:120px">
            <div class="flex items-center justify-between mb-0.5">
                <span class="text-[10px] text-gray-400 dark:text-gray-500">Next: {{ $nextTier->label() }}</span>
                <span class="text-[10px] text-gray-400 dark:text-gray-500">{{ $karmaToNext }} left</span>
            </div>
            <div class="h-1.5 w-full bg-gray-200 dark:bg-navy-700 rounded-full overflow-hidden">
                @php
                    $progress = $nextTier
                        ? min(100, round(($karma - $badge->threshold()) / ($nextTier->threshold() - $badge->threshold()) * 100))
                        : 100;
                @endphp
                <div class="h-full bg-gradient-to-r from-seait-400 to-seait-500 rounded-full transition-all" style="width: {{ $progress }}%"></div>
            </div>
        </div>
    @else
        <p class="text-[10px] text-amber-600 dark:text-amber-400 mt-1 font-semibold">Max tier reached 🏆</p>
    @endif
@endif
```

---

## SUMMARY TABLE

| # | File | What changed |
|---|------|-------------|
| 1 | `layouts/guest.blade.php` | Remove `fill-current text-gray-500`, add StudHub brand subtitle |
| 2 | `welcome.blade.php` | Replace placeholder nav logo with custom SVG |
| 3 | `profile/leaderboard.blade.php` | No emoji medals — rank numbers with gold/silver/bronze colour + shows each user's actual Badge Tier |
| 4 | `requests/index.blade.php` | Card layout, left border urgency colour coding, offer count, fix filter button |
| 5 | `requests/show.blade.php` | Banner header with urgency colour, wraps the DL grid in the new card shell |
| 6 | `resources/shelf.blade.php` | Card layout matching resources index, icon remove button |
| 7 | `feedback/create.blade.php` | Radio tile type selector with emojis, consistent `btn-primary` |
| 8 | `search/index.blade.php` | `.card` results, `section-title` headers with icons, result counts |
| 9 | `profile/show.blade.php` | Karma progress bar to next tier with % fill |
