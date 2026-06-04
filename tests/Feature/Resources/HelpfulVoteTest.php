<?php

use App\Models\LearningResource;
use App\Models\ResourceHelpfulVote;
use App\Models\User;
use Database\Seeders\SeaitCollegesSeeder;
use Database\Seeders\SeaitProgramsSeeder;
use Database\Seeders\SeaitSchoolSeeder;
use Database\Seeders\SeaitSubjectsSeeder;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

beforeEach(function () {
    $this->seed(SeaitSchoolSeeder::class);
    $this->seed(SeaitCollegesSeeder::class);
    $this->seed(SeaitProgramsSeeder::class);
    $this->seed(SeaitSubjectsSeeder::class);

    $this->user = User::factory()->onboarded()->create();
    $this->resource = LearningResource::factory()->create([
        'owner_user_id' => $this->user->id,
        'helpful_count' => 0,
    ]);
});

it('increments helpful_count on first vote', function () {
    DB::transaction(function () {
        ResourceHelpfulVote::create([
            'resource_id' => $this->resource->id,
            'user_id' => $this->user->id,
        ]);
        $this->resource->increment('helpful_count');
    });

    expect($this->resource->fresh()->helpful_count)->toBe(1);
});

it('prevents duplicate vote from same user via unique constraint', function () {
    ResourceHelpfulVote::create([
        'resource_id' => $this->resource->id,
        'user_id' => $this->user->id,
    ]);

    expect(fn () => ResourceHelpfulVote::create([
        'resource_id' => $this->resource->id,
        'user_id' => $this->user->id,
    ]))->toThrow(QueryException::class);
});
