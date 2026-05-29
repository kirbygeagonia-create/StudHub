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
 *   $tier = BadgeTier::fromKarmaOrNull($user->karma);   // null below Seedling threshold
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

    public function icon(): string
    {
        return match ($this) {
            self::Seedling => '🌱',
            self::Bookworm => '📖',
            self::Scribe => '✍️',
            self::Scholar => '🎓',
            self::Illuminator => '💡',
            self::Pathfinder => '🧭',
            self::Sage => '🌙',
            self::Luminary => '⚡',
            self::Archivist => '📚',
            self::Oracle => '🔮',
            self::Custodian => '🏛️',
            self::StudHubLegend => '👑',
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
