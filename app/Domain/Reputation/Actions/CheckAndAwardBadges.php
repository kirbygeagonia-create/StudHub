<?php

namespace App\Domain\Reputation\Actions;

use App\Domain\Catalog\Enums\ResourceType;
use App\Domain\Reputation\Enums\Badge;
use App\Domain\Reputation\Notifications\BadgeEarned;
use App\Models\User;
use App\Models\UserBadge;
use Carbon\Carbon;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\DB;

/**
 * Checks every Badge condition for a given user and awards any that have been
 * newly earned. Already-held badges are skipped (unique constraint + pre-check).
 *
 * Call this after any action that could trigger a badge:
 *   - After uploading a resource   → all Upload badges
 *   - After accepting an offer     → all Fulfill badges
 *   - After recording a lend       → all Lend badges
 *   - After posting a chat message → Community badges
 *   - On login / last_seen update  → Activity badges
 *   - On profile update            → Completionist
 *
 * You may pass a $trigger to run only a subset of checks and avoid
 * unnecessary queries when you know which category changed.
 */
class CheckAndAwardBadges
{
    /** @param 'upload'|'fulfill'|'lend'|'activity'|'community'|'special'|'all' $trigger */
    public function handle(User $user, string $trigger = 'all'): void
    {
        $earned = $user->badges()->pluck('badge')->map(fn ($v) => $v instanceof Badge ? $v->value : $v)->all();

        $candidates = $this->candidatesFor($trigger);

        foreach ($candidates as $badge) {
            if (in_array($badge->value, $earned, true)) {
                continue;
            }

            if ($this->qualifies($user, $badge)) {
                $this->award($user, $badge);
            }
        }
    }

    // ── Candidate selection ──────────────────────────────────────────────────

    /**
     * @return Badge[]
     */
    private function candidatesFor(string $trigger): array
    {
        if ($trigger === 'all') {
            return Badge::cases();
        }

        return array_filter(Badge::cases(), fn (Badge $b) => $b->category()->value === $trigger);
    }

    // ── Qualification checks ─────────────────────────────────────────────────

    private function qualifies(User $user, Badge $badge): bool
    {
        return match ($badge) {

            // ── Upload ───────────────────────────────────────────────────────

            Badge::PageTurner => $this->resourceCount($user) >= 1,

            Badge::StudySupplier => $this->resourceCount($user) >= 5,

            Badge::TheLibrarian => $this->resourceCount($user) >= 25,

            Badge::VaultKeeper => $this->resourceCount($user) >= 50,

            Badge::ModuleMaster => DB::table('learning_resources')
                ->where('owner_user_id', $user->id)
                ->where('type', ResourceType::EModule->value)
                ->count() >= 10,

            Badge::ReviewerRoyale => DB::table('learning_resources')
                ->where('owner_user_id', $user->id)
                ->where('type', ResourceType::Reviewer->value)
                ->count() >= 10,

            Badge::ContentMill => DB::table('learning_resources')
                ->where('owner_user_id', $user->id)
                ->where('created_at', '>=', now()->subDays(7))
                ->count() >= 10,

            Badge::CrossPollinator => DB::table('learning_resources')
                ->where('owner_user_id', $user->id)
                ->where('program_id', '!=', $user->program_id)
                ->whereNotNull('program_id')
                ->exists(),

            Badge::LegacyHolder =>
                // Resource was published 30+ days ago and has at least one save
                DB::table('learning_resources')
                    ->where('owner_user_id', $user->id)
                    ->where('published_at', '<=', now()->subDays(30))
                    ->where('save_count', '>', 0)
                    ->exists(),

            // ── Fulfill ──────────────────────────────────────────────────────

            Badge::FirstResponder => $this->acceptedOfferCount($user) >= 1,

            Badge::RequestSlayer => $this->acceptedOfferCount($user) >= 10,

            Badge::DemandAndSupply => $this->acceptedOfferCount($user) >= 25,

            Badge::TheFixer => $this->acceptedOfferCount($user) >= 50,

            Badge::SignalBoost =>
                // Fulfilled an urgent request within 2 hours of it being posted
                DB::table('offers')
                    ->join('resource_requests', 'offers.request_id', '=', 'resource_requests.id')
                    ->where('offers.offerer_user_id', $user->id)
                    ->where('offers.status', 'accepted')
                    ->where('resource_requests.urgency', 'urgent')
                    ->whereRaw("offers.updated_at <= datetime(resource_requests.created_at, '+2 hours')")
                    ->exists(),

            Badge::BridgeBuilder =>
                // Fulfilled requests from 3+ different programs
                DB::table('offers')
                    ->join('resource_requests', 'offers.request_id', '=', 'resource_requests.id')
                    ->join('users as requesters', 'resource_requests.requester_user_id', '=', 'requesters.id')
                    ->where('offers.offerer_user_id', $user->id)
                    ->where('offers.status', 'accepted')
                    ->whereNotNull('requesters.program_id')
                    ->distinct('requesters.program_id')
                    ->count('requesters.program_id') >= 3,

            // ── Lend ─────────────────────────────────────────────────────────

            Badge::GenerousSoul => $this->lendCount($user) >= 1,

            Badge::BookNomad => $this->lendCount($user) >= 5,

            Badge::TravelingLibrary => $this->lendCount($user) >= 15,

            Badge::SupremeLender => $this->lendCount($user) >= 30,

            // ── Activity ─────────────────────────────────────────────────────

            Badge::EarlyBird =>
                // User was last seen before 7 AM (rough check — updates on each request)
                $user->last_seen_at !== null
                    && (int) $user->last_seen_at->format('G') < 7, // @phpstan-ignore-line — cast to datetime in User model

            Badge::NightOwl =>
                // User was last seen between midnight and 4 AM
                $user->last_seen_at !== null
                    && (int) $user->last_seen_at->format('G') < 4, // @phpstan-ignore-line — cast to datetime in User model

            Badge::DailyDevotee =>
                // Has karma events on 7 distinct calendar days in a row ending today.
                // Streak is approximated via karma_events dates.
                $this->karmaEventStreak($user) >= 7,

            Badge::HabitFormer => $this->karmaEventStreak($user) >= 30,

            Badge::SemesterStrong =>
                // Active (karma event) in every ISO week of the past 18 weeks (~1 semester)
                $this->activeWeeksInLast($user, 18) >= 18,

            Badge::IronCommitment => $this->karmaEventStreak($user) >= 60,

            // ── Community ────────────────────────────────────────────────────

            Badge::Chatterbox => DB::table('chat_messages')
                ->where('sender_id', $user->id)
                ->count() >= 50,

            Badge::ThreadStarter =>
                // Posted the chronologically first message in a chat room at least 10 times.
                // We find chat rooms where this user has the smallest message id.
                DB::table('chat_messages as cm1')
                    ->whereNotExists(function ($q): void {
                        $q->from('chat_messages as cm2')
                            ->whereColumn('cm2.chat_room_id', 'cm1.chat_room_id')
                            ->where('cm2.id', '<', DB::raw('cm1.id'));
                    })
                    ->where('cm1.sender_id', $user->id)
                    ->count() >= 10,

            Badge::TheConnector =>
                // Has @mentioned 20+ distinct users across all messages.
                // We extract @display_name patterns from message bodies.
                $this->distinctMentionCount($user) >= 20,

            Badge::QuickDraw =>
                // Replied within 60 seconds of the previous message in the same room, 10+ times.
                DB::table('chat_messages as reply')
                    ->join('chat_messages as prev', function ($join): void {
                        $join->on('reply.chat_room_id', '=', 'prev.chat_room_id')
                            ->whereColumn('reply.id', '>', 'prev.id');
                    })
                    ->where('reply.sender_id', $user->id)
                    ->where('prev.sender_id', '!=', $user->id)
                    ->whereRaw("reply.created_at <= datetime(prev.created_at, '+60 seconds')")
                    ->whereRaw('prev.id = (
                        SELECT MAX(id) FROM chat_messages
                        WHERE chat_room_id = reply.chat_room_id AND id < reply.id
                    )')
                    ->count() >= 10,

            Badge::RoomRegular =>
                // Has sent at least 1 message in 3+ distinct chat rooms
                DB::table('chat_messages')
                    ->where('sender_id', $user->id)
                    ->distinct('chat_room_id')
                    ->count('chat_room_id') >= 3,

            // ── Special ──────────────────────────────────────────────────────

            Badge::Pioneer =>
                // One of the first 50 registered users on the system
                $user->id <= 50,

            Badge::TheCompletePackage =>
                // Has uploaded, made a request, lent, and chatted
                $this->resourceCount($user) >= 1
                    && DB::table('resource_requests')->where('requester_user_id', $user->id)->exists()
                    && $this->lendCount($user) >= 1
                    && DB::table('chat_messages')->where('sender_id', $user->id)->exists(),

            Badge::NightShift =>
                // Uploaded any resource between midnight and 4 AM (by created_at hour)
                DB::table('learning_resources')
                    ->where('owner_user_id', $user->id)
                    ->whereTime('created_at', '>=', '00:00:00')
                    ->whereTime('created_at', '<=', '03:59:59')
                    ->exists(),

            Badge::Crammer =>
                // 3+ uploads within any single 24-hour rolling window
                DB::table('learning_resources as r1')
                    ->where('r1.owner_user_id', $user->id)
                    ->whereRaw('(
                        SELECT COUNT(*) FROM learning_resources r2
                        WHERE r2.owner_user_id = r1.owner_user_id
                          AND r2.created_at BETWEEN r1.created_at AND datetime(r1.created_at, \'+24 hours\')
                    ) >= 3')
                    ->exists(),

            Badge::ComebackKid =>
                // Has a gap of 30+ days between consecutive karma events,
                // AND has a more recent karma event within the last 30 days.
                DB::table('karma_events as ke1')
                    ->join('karma_events as ke2', function ($join): void {
                        $join->on('ke2.user_id', '=', 'ke1.user_id')
                            ->whereRaw('ke2.created_at > ke1.created_at');
                    })
                    ->where('ke1.user_id', $user->id)
                    ->whereRaw("ke2.created_at >= datetime(ke1.created_at, '+30 days')")
                    ->where('ke2.created_at', '>=', now()->subDays(30))
                    ->exists(),

            Badge::Completionist =>
                // Profile fully filled: name, display_name, program, year_level, college, avatar
                filled($user->name)
                    && filled($user->display_name)
                    && $user->program_id !== null
                    && $user->college_id !== null
                    && $user->year_level !== null
                    && filled($user->avatar_url),

            Badge::Phantom =>
                // 20+ shelf saves but zero uploads
                $this->resourceCount($user) === 0
                    && DB::table('shelf_items')
                        ->join('shelves', 'shelf_items.shelf_id', '=', 'shelves.id')
                        ->where('shelves.user_id', $user->id)
                        ->count() >= 20,
        };
    }

    // ── Award ────────────────────────────────────────────────────────────────

    private function award(User $user, Badge $badge): void
    {
        try {
            $userBadge = UserBadge::create([
                'user_id' => $user->id,
                'badge' => $badge->value,
                'earned_at' => now(),
            ]);

            $user->notify(new BadgeEarned($userBadge));
        } catch (UniqueConstraintViolationException) {
            // Race condition — another request already awarded it. Safe to ignore.
        }
    }

    // ── Helpers ──────────────────────────────────────────────────────────────

    private function resourceCount(User $user): int
    {
        return (int) DB::table('learning_resources')
            ->where('owner_user_id', $user->id)
            ->count();
    }

    private function acceptedOfferCount(User $user): int
    {
        return (int) DB::table('offers')
            ->where('offerer_user_id', $user->id)
            ->where('status', 'accepted')
            ->count();
    }

    private function lendCount(User $user): int
    {
        return (int) DB::table('lends')
            ->where('from_user_id', $user->id)
            ->count();
    }

    /**
     * Counts the current consecutive-day streak ending today using karma_events.
     * A streak day is any calendar date on which at least one karma event exists.
     */
    private function karmaEventStreak(User $user): int
    {
        /** @var array<int, string> $dates */
        $dates = DB::table('karma_events')
            ->where('user_id', $user->id)
            ->selectRaw('DATE(created_at) as d')
            ->distinct()
            ->orderByRaw('DATE(created_at) DESC')
            ->pluck('d')
            ->all();

        if (empty($dates)) {
            return 0;
        }

        $streak = 0;
        $expected = Carbon::today();

        foreach ($dates as $dateStr) {
            $date = Carbon::parse($dateStr);
            if ($date->toDateString() === $expected->toDateString()) {
                $streak++;
                $expected->subDay();
            } else {
                break;
            }
        }

        return $streak;
    }

    /**
     * Count ISO weeks in the past $weeks weeks where the user had at least
     * one karma event (used for SemesterStrong).
     */
    private function activeWeeksInLast(User $user, int $weeks): int
    {
        $dates = DB::table('karma_events')
            ->where('user_id', $user->id)
            ->where('created_at', '>=', now()->subWeeks($weeks))
            ->pluck('created_at');

        $weeksWithActivity = [];
        foreach ($dates as $date) {
            $carbonDate = $date instanceof Carbon ? $date : Carbon::parse($date);
            $isoWeek = $carbonDate->isoWeekYear() . '-W' . str_pad($carbonDate->isoWeek(), 2, '0', STR_PAD_LEFT);
            $weeksWithActivity[$isoWeek] = true;
        }

        return count($weeksWithActivity);
    }

    /**
     * Count distinct users mentioned via @display_name in the user's messages.
     * Matches the pattern @word (alphanumeric + underscore).
     */
    private function distinctMentionCount(User $user): int
    {
        $messages = DB::table('chat_messages')
            ->where('sender_id', $user->id)
            ->pluck('body');

        $mentioned = [];
        foreach ($messages as $body) {
            preg_match_all('/@([\w]+)/', (string) $body, $matches);
            foreach ($matches[1] as $handle) {
                $mentioned[strtolower($handle)] = true;
            }
        }

        return count($mentioned);
    }
}
