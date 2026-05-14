<?php

namespace App\Domain\Catalog\Actions;

use App\Domain\Catalog\Enums\ResourceAvailability;
use App\Domain\Catalog\Enums\ResourceType;
use App\Domain\Catalog\Enums\ResourceVisibility;
use App\Models\LearningResource;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

class SearchResources
{
    /**
     * @param  array{
     *     q?: string|null,
     *     subject_id?: int|null,
     *     type?: string|null,
     *     program_id?: int|null,
     *     year_level?: int|null,
     * }  $filters
     * @return LengthAwarePaginator<LearningResource>
     */
    public function handle(User $viewer, array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        $query = LearningResource::query()
            ->with(['owner:id,display_name,name,program_id', 'subject:id,code,name', 'program:id,code'])
            ->where('school_id', $viewer->school_id)
            ->where('availability', '!=', ResourceAvailability::Archived->value)
            ->whereNull('deleted_at')
            ->where(function (Builder $q) use ($viewer): void {
                $q->where('visibility', ResourceVisibility::School->value)
                    ->orWhere(function (Builder $sub) use ($viewer): void {
                        $sub->where('visibility', ResourceVisibility::ProgramOnly->value)
                            ->where('program_id', $viewer->program_id);
                    })
                    ->orWhere('owner_user_id', $viewer->id);
            });

        $term = isset($filters['q']) ? trim((string) $filters['q']) : '';
        if ($term !== '') {
            $query->search($term);
        }

        if (! empty($filters['subject_id'])) {
            $query->where('subject_id', (int) $filters['subject_id']);
        }

        if (! empty($filters['type']) && in_array($filters['type'], ResourceType::values(), true)) {
            $query->where('type', $filters['type']);
        }

        if (! empty($filters['program_id'])) {
            $query->where('program_id', (int) $filters['program_id']);
        }

        if (! empty($filters['year_level'])) {
            $query->where('year_level', (int) $filters['year_level']);
        }

        return $query->orderByDesc('published_at')
            ->orderByDesc('id')
            ->paginate($perPage)
            ->withQueryString();
    }
}
