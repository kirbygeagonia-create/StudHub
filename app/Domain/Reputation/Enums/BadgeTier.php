<?php

namespace App\Domain\Reputation\Enums;

/**
 * Karma-based tier progression.
 *
 * These tiers are computed dynamically from a user's karma — they are NOT
 * stored in the database. They replace the old Bronze / Silver / Gold system
 * with a 12-tier progression that grows with the SEAIT community over time.
 *
 * Usage:
 *   $tier = BadgeTier::fromKarma($user->karma);          // always returns a tier
 */
enum BadgeTier: string
{
    case Seedling = 'seedling';
    case Bookworm = 'bookworm';
    case Scribe = 'scribe';
    case Scholar = 'scholar';
    case Illuminator = 'illuminator';
    case Pathfinder = 'pathfinder';
    case Sage = 'sage';
    case Luminary = 'luminary';
    case Archivist = 'archivist';
    case Oracle = 'oracle';
    case Custodian = 'custodian';
    case StudHubLegend = 'studhub_legend';

    public function label(): string
    {
        return match ($this) {
            self::Seedling => 'Seedling',
            self::Bookworm => 'Bookworm',
            self::Scribe => 'Scribe',
            self::Scholar => 'Scholar',
            self::Illuminator => 'Illuminator',
            self::Pathfinder => 'Pathfinder',
            self::Sage => 'Sage',
            self::Luminary => 'Luminary',
            self::Archivist => 'Archivist',
            self::Oracle => 'Oracle',
            self::Custodian => 'Custodian',
            self::StudHubLegend => 'StudHub Legend',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::Seedling => 'Just joined StudHub. Everyone starts here.',
            self::Bookworm => 'Started saving resources and building a shelf.',
            self::Scribe => 'Started contributing — the campus is a little richer.',
            self::Scholar => 'A consistent presence. Others know they can count on you.',
            self::Illuminator => 'You bring light to others\' study sessions without being asked.',
            self::Pathfinder => 'You don\'t just share files — you show people the way.',
            self::Sage => 'Deep knowledge, quietly shared. Your uploads speak for themselves.',
            self::Luminary => 'Standout contributor. Your name appears on the leaderboard.',
            self::Archivist => 'You have built something close to a personal library for the campus.',
            self::Oracle => 'People post requests hoping you\'ll see them. You usually do.',
            self::Custodian => 'You have become a pillar of SEAIT\'s knowledge commons.',
            self::StudHubLegend => 'The highest honour. Your contributions have shaped how SEAIT learns.',
        };
    }

    /** Minimum karma required to reach this tier. */
    public function threshold(): int
    {
        return match ($this) {
            self::Seedling => 0,
            self::Bookworm => 25,
            self::Scribe => 75,
            self::Scholar => 150,
            self::Illuminator => 300,
            self::Pathfinder => 500,
            self::Sage => 750,
            self::Luminary => 1_000,
            self::Archivist => 1_500,
            self::Oracle => 2_500,
            self::Custodian => 4_000,
            self::StudHubLegend => 6_000,
        };
    }

    public function rarity(): BadgeRarity
    {
        return match ($this) {
            self::Seedling, self::Bookworm, self::Scribe, self::Scholar => BadgeRarity::Common,
            self::Illuminator, self::Pathfinder, self::Sage => BadgeRarity::Uncommon,
            self::Luminary, self::Archivist => BadgeRarity::Rare,
            self::Oracle, self::Custodian, self::StudHubLegend => BadgeRarity::Legendary,
        };
    }

    /**
     * Returns an SVG path string (Heroicons-style) for the tier icon.
     */
    public function icon(): string
    {
        return match ($this) {
            self::Seedling => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 21V9m0 0 3 3m-3-3-3 3m3-3V3M5 21h14"/>',
            self::Bookworm => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>',
            self::Scribe => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10"/>',
            self::Scholar => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.62 48.62 0 0 1 12 20.904a48.62 48.62 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.636 50.636 0 0 0-2.658-.813A59.906 59.906 0 0 1 12 3.493a59.903 59.903 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0 1 12 13.489a50.702 50.702 0 0 1 7.74-3.342M6.75 15a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Zm0 0v-3.675A55.378 55.378 0 0 1 12 8.443m-7.007 11.55A5.981 5.981 0 0 0 6.75 15.75v-1.5"/>',
            self::Illuminator => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 18v-5.25m0 0a6.01 6.01 0 0 0 1.5-.189m-1.5.189a6.01 6.01 0 0 1-1.5-.189m3.75 7.478a12.06 12.06 0 0 1-4.5 0m3.75 2.383a14.406 14.406 0 0 1-3 0M14.25 18v-.192c0-.983.658-1.823 1.508-2.316a7.5 7.5 0 1 0-7.517 0c.85.493 1.509 1.333 1.509 2.316V18"/>',
            self::Pathfinder => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 6.75V15m6-6v8.25m.503 3.498 4.875-2.437c.381-.19.622-.58.622-1.006V4.82c0-.836-.88-1.38-1.628-1.006l-3.869 1.934c-.317.159-.69.159-1.006 0L9.503 3.252a1.125 1.125 0 0 0-1.006 0L3.622 5.689C3.24 5.88 3 6.27 3 6.695V19.18c0 .836.88 1.38 1.628 1.006l3.869-1.934c.317-.159.69-.159 1.006 0l4.994 2.497c.317.158.69.158 1.006 0Z"/>',
            self::Sage => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21.752 15.002A9.72 9.72 0 0 1 18 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 0 0 3 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 0 0 9.002-5.998Z"/>',
            self::Luminary => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="m3.75 13.5 10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75Z"/>',
            self::Archivist => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 21v-8.25M15.75 21v-8.25M8.25 21v-8.25M3 9l9-6 9 6m-1.5 12V10.332A48.36 48.36 0 0 0 12 9.75c-2.551 0-5.056.2-7.5.582V21M3 21h18M12 6.75h.008v.008H12V6.75Z"/>',
            self::Oracle => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.813 15.904 9 18.75l.813-2.846a4.5 4.5 0 0 1 3.09-3.09l2.846-.814-2.846-.813a4.5 4.5 0 0 1-3.09-3.09L9 5.25l-.813 2.846a4.5 4.5 0 0 1-3.09 3.09L2.25 12l2.846.813a4.5 4.5 0 0 1 3.09 3.09ZM18.259 8.715 18 9.75l.259-1.035a3.375 3.375 0 0 1 2.455-2.456L21.75 6l-1.035-.259a3.375 3.375 0 0 1-2.456-2.456L18 2.25l-.259 1.035a3.375 3.375 0 0 1-2.456 2.456L14.25 6l1.035.259a3.375 3.375 0 0 1 2.456 2.456ZM16.894 20.567 16.5 21.75l.394-1.183a2.25 2.25 0 0 1 1.423-1.423L19.5 18.75l-1.183-.394a2.25 2.25 0 0 1-1.423-1.423l-.394-1.183-.394 1.183a2.25 2.25 0 0 1-1.423 1.423l-1.183.394 1.183.394a2.25 2.25 0 0 1 1.423 1.423Z"/>',
            self::Custodian => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z"/>',
            self::StudHubLegend => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16.5 18.75h-9m9 0a3 3 0 0 1 3 3h-15a3 3 0 0 1 3-3m9 0v-3.375c0-.621-.503-1.125-1.125-1.125h-.871M7.5 18.75v-3.375c0-.621.504-1.125 1.125-1.125h.872m5.007 0H9.497m5.007 0a7.454 7.454 0 0 1-.982-3.172M9.497 14.25a7.454 7.454 0 0 0 .981-3.172M5.25 4.236c-.982.143-1.954.317-2.916.52A6.003 6.003 0 0 0 7.73 9.728M5.25 4.236V4.5c0 2.108.966 3.99 2.48 5.228M5.25 4.236V2.721C7.456 2.41 9.71 2.25 12 2.25c2.291 0 4.545.16 6.75.47v1.516M18.75 4.236c.982.143 1.954.317 2.916.52A6.003 6.003 0 0 1 16.27 9.728M18.75 4.236V4.5c0 2.108-.966 3.99-2.48 5.228m0 0a6.023 6.023 0 0 1-2.77.896m0 0a6.022 6.022 0 0 1-2.77-.896m0 0a6.023 6.023 0 0 1-2.77-.896"/>',
        };
    }

    /** Returns the highest tier the given karma qualifies for. Always returns at least Seedling. */
    public static function fromKarma(int $karma): self
    {
        foreach (array_reverse(self::cases()) as $tier) {
            if ($karma >= $tier->threshold()) {
                return $tier;
            }
        }

        return self::Seedling;
    }

    /**
     * BC-compatible alias. Now always returns a tier (Seedling for karma=0).
     *
     * @deprecated Use fromKarma() — Seedling is awarded to everyone on join.
     */
    public static function fromKarmaOrNull(int $karma): ?self
    {
        return self::fromKarma($karma);
    }

    /** Next tier the user should aim for, or null if already at the top. */
    public function next(): ?self
    {
        $cases = self::cases();
        $index = array_search($this, $cases, true);

        return isset($cases[$index + 1]) ? $cases[$index + 1] : null;
    }

    /** Karma still needed to reach the next tier. Returns 0 at top. */
    public function karmaToNext(int $currentKarma): int
    {
        $next = $this->next();

        return $next === null ? 0 : max(0, $next->threshold() - $currentKarma);
    }
}
