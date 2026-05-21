<?php

namespace App\Models\Scopes;

use App\Models\Report;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;

class ReportSchoolScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     *
     * @param  Builder<Report>  $builder
     */
    public function apply(Builder $builder, Model $model): void
    {
        $user = Auth::user();

        if ($user === null || $user === false) {
            return;
        }

        $builder->whereHas('reporter', function (Builder $query) use ($user): void {
            $query->where('school_id', $user->school_id);
        });
    }
}
