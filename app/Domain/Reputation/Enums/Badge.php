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

    /**
     * Returns an SVG path string (Heroicons-style) for the badge icon.
     * These are used in Blade views via <x-icon> or inline SVG rendering.
     */
    public function icon(): string
    {
        return match ($this) {
            // Upload
            self::PageTurner => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z"/>',
            self::StudySupplier => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="m20.25 7.5-.625 10.632a2.25 2.25 0 0 1-2.247 2.118H6.622a2.25 2.25 0 0 1-2.247-2.118L3.75 7.5m8.25 3v6.75m0 0-3-3m3 3 3-3M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125Z"/>',
            self::TheLibrarian => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 21v-8.25M15.75 21v-8.25M8.25 21v-8.25M3 9l9-6 9 6m-1.5 12V10.332A48.36 48.36 0 0 0 12 9.75c-2.551 0-5.056.2-7.5.582V21M3 21h18M12 6.75h.008v.008H12V6.75Z"/>',
            self::VaultKeeper => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z"/>',
            self::ModuleMaster => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 17.25v1.007a3 3 0 0 1-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0 1 15 18.257V17.25m6-12V15a2.25 2.25 0 0 1-2.25 2.25H5.25A2.25 2.25 0 0 1 3 15V5.25m18 0A2.25 2.25 0 0 0 18.75 3H5.25A2.25 2.25 0 0 0 3 5.25m18 0V12a2.25 2.25 0 0 1-2.25 2.25H5.25A2.25 2.25 0 0 1 3 12V5.25"/>',
            self::ReviewerRoyale => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25ZM6.75 12h.008v.008H6.75V12Zm0 3h.008v.008H6.75V15Zm0 3h.008v.008H6.75V18Z"/>',
            self::ContentMill => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75Z"/>',
            self::CrossPollinator => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 21a9.004 9.004 0 0 0 8.716-6.747M12 21a9.004 9.004 0 0 1-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 0 1 7.843 4.582M12 3a8.997 8.997 0 0 0-7.843 4.582m15.686 0A11.953 11.953 0 0 1 12 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0 1 21 12c0 .778-.099 1.533-.284 2.253m0 0A17.919 17.919 0 0 1 12 16.5c-3.162 0-6.133-.815-8.716-2.247m0 0A9.015 9.015 0 0 1 3 12c0-1.605.42-3.113 1.157-4.418"/>',
            self::LegacyHolder => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>',
            // Fulfill
            self::FirstResponder => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0"/>',
            self::RequestSlayer => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75Z"/>',
            self::DemandAndSupply => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.25 18L9 11.25l4.306 4.306a11.95 11.95 0 0 1 5.814-5.518l2.74-1.22m0 0-5.94-2.281m5.94 2.28-2.28 5.941"/>',
            self::TheFixer => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11.42 15.17 17.25 21A2.652 2.652 0 0 0 21 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655 5.653a2.548 2.548 0 1 1-3.586-3.586l6.837-5.63m5.108-.233c.55-.164 1.163-.188 1.743-.14a4.5 4.5 0 0 0 4.486-6.336l-3.276 3.277a3.004 3.004 0 0 1-2.25-2.25l3.276-3.276a4.5 4.5 0 0 0-6.336 4.486c.091 1.076-.071 2.264-.904 2.95l-.102.085m-1.745 1.437L5.909 7.5H4.5L2.25 3.75l1.5-1.5L7.5 4.5v1.409l4.087 4.113"/>',
            self::SignalBoost => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 18v-5.25m0 0a6.01 6.01 0 0 0 1.5-.189m-1.5.189a6.01 6.01 0 0 1-1.5-.189m3.75 7.478a12.06 12.06 0 0 1-4.5 0m3.75 2.383a14.406 14.406 0 0 1-3 0M14.25 18v-.192c0-.983.658-1.823 1.508-2.316a7.5 7.5 0 1 0-7.517 0c.85.493 1.509 1.333 1.509 2.316V18"/>',
            self::BridgeBuilder => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8.25 21v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21m0 0h4.5V3.545M12.75 21h7.5V10.75M2.25 21h1.5m18 0h-18M2.25 9l4.5-1.636M18.75 3l-1.5.545m0 6.205 3 1m1.5.5-1.5-.5M6.75 7.364V3h-3v18m3-13.636 10.5-3.819"/>',
            // Lend
            self::GenerousSoul => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z"/>',
            self::BookNomad => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 6.75V15m6-6v8.25m.503 3.498 4.875-2.437c.381-.19.622-.58.622-1.006V4.82c0-.836-.88-1.38-1.628-1.006l-3.869 1.934c-.317.159-.69.159-1.006 0L9.503 3.252a1.125 1.125 0 0 0-1.006 0L3.622 5.689C3.24 5.88 3 6.27 3 6.695V19.18c0 .836.88 1.38 1.628 1.006l3.869-1.934c.317-.159.69-.159 1.006 0l4.994 2.497c.317.158.69.158 1.006 0Z"/>',
            self::TravelingLibrary => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8.25 18.75a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 0 1-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 0 0-3.213-9.193 2.056 2.056 0 0 0-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 0 0-10.026 0 1.106 1.106 0 0 0-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12"/>',
            self::SupremeLender => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16.5 18.75h-9m9 0a3 3 0 0 1 3 3h-15a3 3 0 0 1 3-3m9 0v-3.375c0-.621-.503-1.125-1.125-1.125h-.871M7.5 18.75v-3.375c0-.621.504-1.125 1.125-1.125h.872m5.007 0H9.497m5.007 0a7.454 7.454 0 0 1-.982-3.172M9.497 14.25a7.454 7.454 0 0 0 .981-3.172M5.25 4.236c-.982.143-1.954.317-2.916.52A6.003 6.003 0 0 0 7.73 9.728M5.25 4.236V4.5c0 2.108.966 3.99 2.48 5.228M5.25 4.236V2.721C7.456 2.41 9.71 2.25 12 2.25c2.291 0 4.545.16 6.75.47v1.516M18.75 4.236c.982.143 1.954.317 2.916.52A6.003 6.003 0 0 1 16.27 9.728M18.75 4.236V4.5c0 2.108-.966 3.99-2.48 5.228m0 0a6.023 6.023 0 0 1-2.77.896m0 0a6.022 6.022 0 0 1-2.77-.896m0 0a6.023 6.023 0 0 1-2.77-.896"/>',
            // Activity
            self::EarlyBird => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 3v2.25m6.364.386-1.591 1.591M21 12h-2.25m-.386 6.364-1.591-1.591M12 18.75V21m-4.773-4.227-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0Z"/>',
            self::NightOwl => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21.752 15.002A9.72 9.72 0 0 1 18 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 0 0 3 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 0 0 9.002-5.998Z"/>',
            self::DailyDevotee => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5m-9-6h.008v.008H12v-.008ZM12 15h.008v.008H12V15Zm0 2.25h.008v.008H12v-.008ZM9.75 15h.008v.008H9.75V15Zm0 2.25h.008v.008H9.75v-.008ZM7.5 15h.008v.008H7.5V15Zm0 2.25h.008v.008H7.5v-.008Zm6.75-4.5h.008v.008h-.008v-.008Zm0 2.25h.008v.008h-.008V15Zm0 2.25h.008v.008h-.008v-.008Zm2.25-4.5h.008v.008H16.5v-.008Zm0 2.25h.008v.008H16.5V15Z"/>',
            self::HabitFormer => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.362 5.214A8.252 8.252 0 0 1 12 21 8.25 8.25 0 0 1 6.038 7.047 8.287 8.287 0 0 0 9 9.601a8.983 8.983 0 0 1 3.361-6.867 8.21 8.21 0 0 0 3 2.48Z"/>',
            self::SemesterStrong => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11.48 3.499a.562.562 0 0 1 1.04 0l2.125 5.111a.563.563 0 0 0 .475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 0 0-.182.557l1.285 5.385a.562.562 0 0 1-.84.61l-4.725-2.885a.562.562 0 0 0-.586 0L6.982 20.54a.562.562 0 0 1-.84-.61l1.285-5.386a.562.562 0 0 0-.182-.557l-4.204-3.602a.562.562 0 0 1 .321-.988l5.518-.442a.563.563 0 0 0 .475-.345L11.48 3.5Z"/>',
            self::IronCommitment => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 9.563C9 9.252 9.252 9 9.563 9h4.874c.311 0 .563.252.563.563v4.874c0 .311-.252.563-.563.563H9.564A.562.562 0 0 1 9 14.437V9.564Z"/>',
            // Community
            self::Chatterbox => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20.25 8.511c.884.284 1.5 1.128 1.5 2.097v4.286c0 1.136-.847 2.1-1.98 2.193-.34.027-.68.052-1.02.072v3.091l-3-3c-1.354 0-2.694-.055-4.02-.163a2.115 2.115 0 0 1-.825-.242m9.345-8.334a2.126 2.126 0 0 0-.476-.095 48.64 48.64 0 0 0-8.048 0c-1.131.094-1.976 1.057-1.976 2.192v4.286c0 .837.46 1.58 1.155 1.951m9.345-8.334V6.637c0-1.621-1.152-3.026-2.76-3.235A48.455 48.455 0 0 0 11.25 3c-2.115 0-4.198.137-6.24.402-1.608.209-2.76 1.614-2.76 3.235v6.226c0 1.621 1.152 3.026 2.76 3.235.577.075 1.157.14 1.74.194V21l4.155-4.155"/>',
            self::ThreadStarter => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10.34 15.84c-.688-.06-1.386-.09-2.09-.09H7.5a4.5 4.5 0 0 1-4.5-4.5v-2.34m13.28 13.28a3.75 3.75 0 0 1-3.75 3.75h-1.5a3.75 3.75 0 0 1-3.75-3.75v-1.5c0-1.036.84-1.875 1.875-1.875h3.75c1.036 0 1.875.84 1.875 1.875v1.5Zm0 0 3.75-3.75M4.5 9.75h2.25a3.75 3.75 0 0 0 3.75-3.75V3.75"/>',
            self::TheConnector => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z"/>',
            self::QuickDraw => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="m3.75 13.5 10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75Z"/>',
            self::RoomRegular => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3.75h.008v.008h-.008v-.008Zm0 3h.008v.008h-.008v-.008Zm0 3h.008v.008h-.008v-.008Z"/>',
            // Special
            self::Pioneer => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 18v-5.25m0 0a6.01 6.01 0 0 0 1.5-.189m-1.5.189a6.01 6.01 0 0 1-1.5-.189m3.75 7.478a12.06 12.06 0 0 1-4.5 0m3.75 2.383a14.406 14.406 0 0 1-3 0M14.25 18v-.192c0-.983.658-1.823 1.508-2.316a7.5 7.5 0 1 0-7.517 0c.85.493 1.509 1.333 1.509 2.316V18"/>',
            self::TheCompletePackage => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="m20.25 7.5-.625 10.632a2.25 2.25 0 0 1-2.247 2.118H6.622a2.25 2.25 0 0 1-2.247-2.118L3.75 7.5m6 4.125 2.25 2.25m0 0 2.25-2.25M12 13.875V3"/>',
            self::NightShift => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21.752 15.002A9.72 9.72 0 0 1 18 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 0 0 3 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 0 0 9.002-5.998Z"/>',
            self::Crammer => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>',
            self::ComebackKid => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 15 3 9m0 0 6-6M3 9h12a6 6 0 0 1 0 12h-3"/>',
            self::Completionist => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>',
            self::Phantom => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.53 16.122a3 3 0 0 0-5.78 1.128 2.25 2.25 0 0 0 2.4 2.245 4.5 4.5 0 0 0 8.4-2.245c0-.399-.078-.78-.22-1.128m0 0a15.998 15.998 0 0 0 3.388-1.62m-5.043-.025a15.994 15.994 0 0 1 1.622-3.395m3.42 3.42a15.995 15.995 0 0 0 4.764-4.648l3.876-5.814a1.151 1.151 0 0 0-1.597-1.597L14.146 6.32a15.996 15.996 0 0 0-4.649 4.763m3.42 3.42a6.776 6.776 0 0 0-3.42-3.42Z"/>',
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
