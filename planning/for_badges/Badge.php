<?php

namespace App\Domain\Reputation\Enums;

/**
 * Achievement badges — stored in the user_badges table.
 *
 * These are earned once by completing specific actions. They are separate from
 * BadgeTier, which is computed dynamically from karma and never stored.
 *
 * Each case has a value that becomes the stored string in user_badges.badge.
 * Add new badges freely — the migration uses a string column, so no ALTER needed.
 */
enum Badge: string
{
    // ── Upload badges ────────────────────────────────────────────────────────
    case PageTurner = 'page_turner';
    case StudySupplier = 'study_supplier';
    case TheLibrarian = 'the_librarian';
    case VaultKeeper = 'vault_keeper';
    case ModuleMaster = 'module_master';
    case ReviewerRoyale = 'reviewer_royale';
    case ContentMill = 'content_mill';
    case CrossPollinator = 'cross_pollinator';
    case LegacyHolder = 'legacy_holder';

    // ── Request fulfillment badges ───────────────────────────────────────────
    case FirstResponder = 'first_responder';
    case RequestSlayer = 'request_slayer';
    case DemandAndSupply = 'demand_and_supply';
    case TheFixer = 'the_fixer';
    case SignalBoost = 'signal_boost';
    case BridgeBuilder = 'bridge_builder';

    // ── Lending badges ───────────────────────────────────────────────────────
    case GenerousSoul = 'generous_soul';
    case BookNomad = 'book_nomad';
    case TravelingLibrary = 'traveling_library';
    case SupremeLender = 'supreme_lender';

    // ── Activity & streak badges ─────────────────────────────────────────────
    case EarlyBird = 'early_bird';
    case NightOwl = 'night_owl';
    case DailyDevotee = 'daily_devotee';
    case HabitFormer = 'habit_former';
    case SemesterStrong = 'semester_strong';
    case IronCommitment = 'iron_commitment';

    // ── Community & chat badges ──────────────────────────────────────────────
    case Chatterbox = 'chatterbox';
    case ThreadStarter = 'thread_starter';
    case TheConnector = 'the_connector';
    case QuickDraw = 'quick_draw';
    case RoomRegular = 'room_regular';

    // ── Special / hidden badges ──────────────────────────────────────────────
    case Pioneer = 'pioneer';
    case TheCompletePackage = 'the_complete_package';
    case NightShift = 'night_shift';
    case Crammer = 'crammer';
    case ComebackKid = 'comeback_kid';
    case Completionist = 'completionist';
    case Phantom = 'phantom';

    // ── Metadata ─────────────────────────────────────────────────────────────

    public function label(): string
    {
        return match ($this) {
            // Upload
            self::PageTurner => 'Page Turner',
            self::StudySupplier => 'Study Supplier',
            self::TheLibrarian => 'The Librarian',
            self::VaultKeeper => 'Vault Keeper',
            self::ModuleMaster => 'Module Master',
            self::ReviewerRoyale => 'Reviewer Royale',
            self::ContentMill => 'Content Mill',
            self::CrossPollinator => 'Cross-Pollinator',
            self::LegacyHolder => 'Legacy Holder',
            // Fulfill
            self::FirstResponder => 'First Responder',
            self::RequestSlayer => 'Request Slayer',
            self::DemandAndSupply => 'Demand & Supply',
            self::TheFixer => 'The Fixer',
            self::SignalBoost => 'Signal Boost',
            self::BridgeBuilder => 'Bridge Builder',
            // Lend
            self::GenerousSoul => 'Generous Soul',
            self::BookNomad => 'Book Nomad',
            self::TravelingLibrary => 'Traveling Library',
            self::SupremeLender => 'Supreme Lender',
            // Activity
            self::EarlyBird => 'Early Bird',
            self::NightOwl => 'Night Owl',
            self::DailyDevotee => 'Daily Devotee',
            self::HabitFormer => 'Habit Former',
            self::SemesterStrong => 'Semester Strong',
            self::IronCommitment => 'Iron Commitment',
            // Community
            self::Chatterbox => 'Chatterbox',
            self::ThreadStarter => 'Thread Starter',
            self::TheConnector => 'The Connector',
            self::QuickDraw => 'Quick Draw',
            self::RoomRegular => 'Room Regular',
            // Special
            self::Pioneer => 'Pioneer',
            self::TheCompletePackage => 'The Complete Package',
            self::NightShift => 'Night Shift',
            self::Crammer => 'Crammer',
            self::ComebackKid => 'Comeback Kid',
            self::Completionist => 'Completionist',
            self::Phantom => 'Phantom',
        };
    }

    public function description(): string
    {
        return match ($this) {
            // Upload
            self::PageTurner => 'Uploaded your very first resource. The journey starts with one file.',
            self::StudySupplier => 'Maintaining a steady supply of study material for your classmates.',
            self::TheLibrarian => 'Built a collection that rivals the school library (well, almost).',
            self::VaultKeeper => 'Your uploads are practically a full semester\'s worth of material.',
            self::ModuleMaster => 'Specialist in digital learning — your e-modules are always in demand.',
            self::ReviewerRoyale => 'Your reviewers have helped more people pass than a cram school ever could.',
            self::ContentMill => 'A week of unstoppable output. The campus thanks you for your dedication.',
            self::CrossPollinator => 'Shared resources for a program other than your own. True campus citizen.',
            self::LegacyHolder => 'Resources you uploaded months ago are still being saved and downloaded.',
            // Fulfill
            self::FirstResponder => 'Answered someone\'s call for help before anyone else did.',
            self::RequestSlayer => 'Ten requests down. The board is a little cleaner because of you.',
            self::DemandAndSupply => 'A reliable force keeping the StudHub ecosystem alive and flowing.',
            self::TheFixer => 'You\'ve become the go-to solver. Half the board gets cleared by you.',
            self::SignalBoost => 'Responded to an urgent request and saved someone\'s exam prep.',
            self::BridgeBuilder => 'Connected needs across different programs — a true campus bridge.',
            // Lend
            self::GenerousSoul => 'Trusted someone enough to hand over your actual physical book.',
            self::BookNomad => 'Your books travel the campus more than you do.',
            self::TravelingLibrary => 'A mobile lending service of one. Your collection is always somewhere useful.',
            self::SupremeLender => 'The highest lending honour. Your generosity has set the benchmark.',
            // Activity
            self::EarlyBird => 'Who studies before 7am? You do. Apparently without being forced.',
            self::NightOwl => 'Your best ideas come after midnight. Science might agree.',
            self::DailyDevotee => 'Seven days straight. Consistency is a skill and you have it.',
            self::HabitFormer => 'Thirty days of showing up. StudHub is officially part of your routine.',
            self::SemesterStrong => 'Active every week of an entire semester. That is iron discipline.',
            self::IronCommitment => 'Sixty consecutive days. You and StudHub are basically partners.',
            // Community
            self::Chatterbox => 'You keep the program channels alive with 50 messages and counting.',
            self::ThreadStarter => 'Ten conversations you kicked off. You ask the questions others feared to.',
            self::TheConnector => 'You have @mentioned 20 different people. StudHub is more social because of you.',
            self::QuickDraw => 'Lightning-fast replies, ten times over. You were there when it mattered.',
            self::RoomRegular => 'A familiar face across multiple chat rooms. Your program can\'t claim you fully.',
            // Special
            self::Pioneer => 'You were here before most people even knew StudHub existed.',
            self::TheCompletePackage => 'Upload. Request. Lend. Chat. You\'ve done it all — a true all-rounder.',
            self::NightShift => 'Uploaded a resource between midnight and 4am. We hope you slept eventually.',
            self::Crammer => 'Three uploads in one day. Finals panic or pure dedication — both count.',
            self::ComebackKid => 'Gone for a month, back like nothing happened. Welcome back.',
            self::Completionist => 'Every profile field filled. You believe details matter, and you are right.',
            self::Phantom => 'Twenty saves, zero uploads. The campus gives; you receive. Balance is overrated.',
        };
    }

    public function earnCondition(): string
    {
        return match ($this) {
            // Upload
            self::PageTurner => 'Upload your first resource',
            self::StudySupplier => 'Upload 5 resources',
            self::TheLibrarian => 'Upload 25 resources',
            self::VaultKeeper => 'Upload 50 resources',
            self::ModuleMaster => 'Upload 10 e-modules',
            self::ReviewerRoyale => 'Upload 10 reviewers',
            self::ContentMill => 'Upload 10 resources in a single week',
            self::CrossPollinator => 'Upload a resource tagged to a different program',
            self::LegacyHolder => 'Have a resource saved 30+ days after it was posted',
            // Fulfill
            self::FirstResponder => 'Fulfill your first request',
            self::RequestSlayer => 'Fulfill 10 requests',
            self::DemandAndSupply => 'Fulfill 25 requests',
            self::TheFixer => 'Fulfill 50 requests',
            self::SignalBoost => 'Fulfill an urgent request within 2 hours of posting',
            self::BridgeBuilder => 'Fulfill requests from 3 or more different programs',
            // Lend
            self::GenerousSoul => 'Complete your first physical book lend',
            self::BookNomad => 'Lend 5 books',
            self::TravelingLibrary => 'Lend 15 books',
            self::SupremeLender => 'Lend 30 books',
            // Activity
            self::EarlyBird => 'Log in before 7:00 AM',
            self::NightOwl => 'Be active after midnight',
            self::DailyDevotee => 'Log in for 7 consecutive days',
            self::HabitFormer => 'Log in for 30 consecutive days',
            self::SemesterStrong => 'Active every week of an entire semester',
            self::IronCommitment => 'Log in for 60 consecutive days',
            // Community
            self::Chatterbox => 'Send 50 chat messages',
            self::ThreadStarter => 'Post the first message in 10 different chat threads',
            self::TheConnector => '@mention 20 different users',
            self::QuickDraw => 'Reply within 1 minute, 10 different times',
            self::RoomRegular => 'Send messages in 3 or more chat rooms',
            // Special
            self::Pioneer => 'Be one of the first 50 users to register',
            self::TheCompletePackage => 'Upload, request, lend, and chat — all four modules',
            self::NightShift => 'Upload a resource between midnight and 4:00 AM',
            self::Crammer => 'Upload 3 or more resources within 24 hours',
            self::ComebackKid => 'Return after 30 or more days of inactivity',
            self::Completionist => 'Fill in every field on your profile',
            self::Phantom => 'Save 20 resources without uploading any',
        };
    }

    public function category(): BadgeCategory
    {
        return match ($this) {
            self::PageTurner, self::StudySupplier, self::TheLibrarian,
            self::VaultKeeper, self::ModuleMaster, self::ReviewerRoyale,
            self::ContentMill, self::CrossPollinator, self::LegacyHolder => BadgeCategory::Upload,

            self::FirstResponder, self::RequestSlayer, self::DemandAndSupply,
            self::TheFixer, self::SignalBoost, self::BridgeBuilder => BadgeCategory::Fulfill,

            self::GenerousSoul, self::BookNomad, self::TravelingLibrary,
            self::SupremeLender => BadgeCategory::Lend,

            self::EarlyBird, self::NightOwl, self::DailyDevotee,
            self::HabitFormer, self::SemesterStrong, self::IronCommitment => BadgeCategory::Activity,

            self::Chatterbox, self::ThreadStarter, self::TheConnector,
            self::QuickDraw, self::RoomRegular => BadgeCategory::Community,

            self::Pioneer, self::TheCompletePackage, self::NightShift,
            self::Crammer, self::ComebackKid, self::Completionist, self::Phantom => BadgeCategory::Special,
        };
    }

    public function rarity(): BadgeRarity
    {
        return match ($this) {
            // Common
            self::PageTurner, self::StudySupplier, self::FirstResponder,
            self::GenerousSoul, self::Chatterbox => BadgeRarity::Common,

            // Uncommon
            self::ModuleMaster, self::ReviewerRoyale, self::CrossPollinator,
            self::RequestSlayer, self::SignalBoost, self::BookNomad,
            self::EarlyBird, self::NightOwl, self::DailyDevotee,
            self::ThreadStarter, self::TheConnector, self::RoomRegular => BadgeRarity::Uncommon,

            // Rare
            self::TheLibrarian, self::ContentMill, self::LegacyHolder,
            self::DemandAndSupply, self::BridgeBuilder, self::TravelingLibrary,
            self::HabitFormer, self::SemesterStrong, self::QuickDraw => BadgeRarity::Rare,

            // Legendary
            self::VaultKeeper, self::TheFixer, self::SupremeLender,
            self::IronCommitment => BadgeRarity::Legendary,

            // Hidden
            self::Pioneer, self::TheCompletePackage, self::NightShift,
            self::Crammer, self::ComebackKid, self::Completionist, self::Phantom => BadgeRarity::Hidden,
        };
    }

    public function icon(): string
    {
        return match ($this) {
            // Upload
            self::PageTurner => '📄',
            self::StudySupplier => '📦',
            self::TheLibrarian => '🏛️',
            self::VaultKeeper => '🔐',
            self::ModuleMaster => '💻',
            self::ReviewerRoyale => '📋',
            self::ContentMill => '🚀',
            self::CrossPollinator => '🌐',
            self::LegacyHolder => '🕰️',
            // Fulfill
            self::FirstResponder => '🚨',
            self::RequestSlayer => '⚔️',
            self::DemandAndSupply => '⚖️',
            self::TheFixer => '🔧',
            self::SignalBoost => '📡',
            self::BridgeBuilder => '🌉',
            // Lend
            self::GenerousSoul => '🤝',
            self::BookNomad => '🗺️',
            self::TravelingLibrary => '🚌',
            self::SupremeLender => '🏆',
            // Activity
            self::EarlyBird => '☕',
            self::NightOwl => '🦉',
            self::DailyDevotee => '📅',
            self::HabitFormer => '🔥',
            self::SemesterStrong => '⭐',
            self::IronCommitment => '♾️',
            // Community
            self::Chatterbox => '💬',
            self::ThreadStarter => '📢',
            self::TheConnector => '🕸️',
            self::QuickDraw => '⚡',
            self::RoomRegular => '🚪',
            // Special
            self::Pioneer => '🌿',
            self::TheCompletePackage => '📦',
            self::NightShift => '🌙',
            self::Crammer => '⏳',
            self::ComebackKid => '↩️',
            self::Completionist => '✅',
            self::Phantom => '👻',
        };
    }

    /** @return array<int, string> */
    public static function values(): array
    {
        return array_map(fn (self $c) => $c->value, self::cases());
    }

    /** @return Badge[] */
    public static function byCategory(BadgeCategory $category): array
    {
        return array_filter(self::cases(), fn (self $b) => $b->category() === $category);
    }
}
