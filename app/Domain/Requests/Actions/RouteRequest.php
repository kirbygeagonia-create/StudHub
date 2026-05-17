<?php

namespace App\Domain\Requests\Actions;

use App\Domain\Requests\Jobs\NotifyRoutedUsers;
use App\Models\LearningResource;
use App\Models\Program;
use App\Models\Request;
use App\Models\RequestRoute;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class RouteRequest
{
    private const W_EDGE = 0.40;
    private const W_RESOURCE = 0.25;
    private const W_HISTORY = 0.20;
    private const W_PROXIMITY = 0.10;
    private const W_URGENCY = 0.05;
    private const PENALTY_SELF = 0.05;

    private const PROGRAM_THRESHOLD = 0.35;
    private const CHAT_THRESHOLD = 0.65;

    private const MAX_USERS_PER_PROGRAM = 8;
    private const GLOBAL_USER_CAP = 25;

    public function handle(Request $request): void
    {
        $subject = $request->subject;
        $requester = $request->requester;

        $programs = Program::whereHas('subjects', function (Builder $q) use ($subject): void {
            $q->where('subject_id', $subject->id);
        })->with(['subjects' => function (BelongsToMany $q) use ($subject): void {
            $q->where('subject_id', $subject->id)
                ->withPivot(['typical_year_level', 'weight']);
        }])->get();

        if ($programs->isEmpty()) {
            $this->routeToOwnProgramOnly($request, $subject);
            return;
        }

        $scoredPrograms = collect();

        foreach ($programs as $program) {
            $pivot = $program->subjects->first()?->pivot;

            $edgeWeight = $pivot?->weight ?? 0.0;
            $typicalYear = $pivot?->typical_year_level ?? 1;

            $score = self::W_EDGE * $edgeWeight
                + self::W_RESOURCE * $this->normalizedResourceCount($program, $subject)
                + self::W_HISTORY * $this->historicalFulfillmentRate($program, $subject)
                + self::W_PROXIMITY * $this->yearProximityBonus($typicalYear, $requester->year_level)
                + self::W_URGENCY * $this->urgencyMultiplier($request->urgency?->value ?? 'normal');

            if ($program->id === $requester->program_id) {
                $score -= self::PENALTY_SELF;
            }

            if ($score >= self::PROGRAM_THRESHOLD) {
                $scoredPrograms->push([
                    'program' => $program,
                    'score' => round($score, 3),
                    'typical_year' => $typicalYear,
                ]);
            }
        }

        $scoredPrograms = $scoredPrograms->sortByDesc('score')->values();

        $allUserIds = collect();

        DB::transaction(function () use ($request, $scoredPrograms, $subject, &$allUserIds): void {
            foreach ($scoredPrograms as $row) {
                $pickedUsers = $this->pickUsersToNotify($row['program'], $subject, $request, $row['typical_year']);

                $remaining = self::GLOBAL_USER_CAP - $allUserIds->count();
                if ($remaining <= 0) {
                    $pickedUsers = collect();
                } elseif ($pickedUsers->count() > $remaining) {
                    $pickedUsers = $pickedUsers->take($remaining);
                }

                RequestRoute::create([
                    'request_id' => $request->id,
                    'program_id' => $row['program']->id,
                    'score' => $row['score'],
                    'notified_user_count' => $pickedUsers->count(),
                ]);

                $allUserIds = $allUserIds->merge($pickedUsers->pluck('id'));
            }
        });

        if ($allUserIds->isNotEmpty()) {
            NotifyRoutedUsers::dispatch($request->id, $allUserIds->unique()->values()->all());
        }

        if ($request->urgency?->value === 'urgent') {
            foreach ($scoredPrograms as $row) {
                if ($row['score'] >= self::CHAT_THRESHOLD) {
                    \App\Domain\Requests\Jobs\CrossPostRequest::dispatch($request->id, $row['program']->id);
                }
            }
        }
    }

    /**
     * @return array<string, float|int>
     */
    public static function routingConstants(): array
    {
        return [
            'w_edge' => self::W_EDGE,
            'w_resource' => self::W_RESOURCE,
            'w_history' => self::W_HISTORY,
            'w_proximity' => self::W_PROXIMITY,
            'w_urgency' => self::W_URGENCY,
            'penalty_self' => self::PENALTY_SELF,
            'program_threshold' => self::PROGRAM_THRESHOLD,
            'chat_threshold' => self::CHAT_THRESHOLD,
            'max_users_per_program' => self::MAX_USERS_PER_PROGRAM,
            'global_user_cap' => self::GLOBAL_USER_CAP,
        ];
    }

    private function routeToOwnProgramOnly(Request $request, mixed $subject): void
    {
        $ownProgram = $request->requester->program;

        if ($ownProgram === null) {
            return;
        }

        $users = $this->pickUsersToNotify($ownProgram, $subject, $request, null);

        DB::transaction(function () use ($request, $ownProgram, $users): void {
            RequestRoute::create([
                'request_id' => $request->id,
                'program_id' => $ownProgram->id,
                'score' => self::PROGRAM_THRESHOLD,
                'notified_user_count' => $users->count(),
            ]);
        });

        if ($users->isNotEmpty()) {
            NotifyRoutedUsers::dispatch($request->id, $users->pluck('id')->all());
        }
    }

    private function normalizedResourceCount(Program $program, mixed $subject): float
    {
        $max = LearningResource::where('subject_id', $subject->id)
            ->where('availability', '!=', 'archived')
            ->count();

        if ($max === 0) {
            return 0.0;
        }

        $programCount = LearningResource::where('subject_id', $subject->id)
            ->where('program_id', $program->id)
            ->where('availability', '!=', 'archived')
            ->count();

        return $max > 0 ? $programCount / $max : 0.0;
    }

    private function historicalFulfillmentRate(Program $program, mixed $subject): float
    {
        return 0.0;
    }

    private function yearProximityBonus(int $typicalYear, ?int $requesterYearLevel): float
    {
        if ($requesterYearLevel === null) {
            return 0.0;
        }

        $diff = abs($typicalYear - $requesterYearLevel);

        if ($diff <= 1) {
            return 1.0;
        }

        if ($diff === 2) {
            return 0.5;
        }

        return 0.0;
    }

    private function urgencyMultiplier(string $urgency): float
    {
        return match ($urgency) {
            'urgent' => 1.0,
            'low' => 0.3,
            default => 0.5,
        };
    }

    /**
     * @return Collection<int, User>
     */
    private function pickUsersToNotify(Program $program, mixed $subject, Request $request, ?int $typicalYear): Collection
    {
        $candidates = User::where('program_id', $program->id)
            ->where('id', '!=', $request->requester_user_id)
            ->whereNotNull('onboarded_at')
            ->get();

        $subjectId = $subject->id;
        $typeWanted = $request->type_wanted;

        $scored = $candidates->map(function (User $user) use ($subjectId, $typeWanted, $typicalYear, $request): array {
            $score = 0.0;

            if ($typicalYear !== null && $user->year_level !== null && $user->year_level < $typicalYear) {
                return ['user' => $user, 'score' => -999.0];
            }

            $hasMatchingResource = LearningResource::where('owner_user_id', $user->id)
                ->where('subject_id', $subjectId)
                ->where('type', $typeWanted)
                ->where('availability', '!=', 'archived')
                ->exists();

            if ($hasMatchingResource) {
                $score += 1.0;
            }

            $score += ($user->karma ?? 0) > 0 ? min(0.2, ($user->karma / 500) * 0.2) : 0.0;

            $recentNotifications = $user->notifications()
                ->where('type', 'App\Domain\Requests\Jobs\NotifyRoutedUsers')
                ->where('created_at', '>=', now()->subHours(24))
                ->count();

            if ($recentNotifications > 0) {
                $score -= 0.5;
            }

            $todayNotifications = $user->notifications()
                ->where('type', 'App\Domain\Requests\Jobs\NotifyRoutedUsers')
                ->where('created_at', '>=', now()->startOfDay())
                ->count();

            if ($todayNotifications >= 3) {
                return ['user' => $user, 'score' => -999.0];
            }

            return ['user' => $user, 'score' => $score];
        });

        return $scored
            ->sortByDesc('score')
            ->take(self::MAX_USERS_PER_PROGRAM)
            ->reject(fn (array $item) => $item['score'] < 0)
            ->map(fn (array $item) => $item['user']);
    }
}