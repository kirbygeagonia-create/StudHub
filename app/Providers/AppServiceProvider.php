<?php

namespace App\Providers;

use App\Models\ChatMessage;
use App\Models\LearningResource;
use App\Models\Offer;
use App\Models\Report;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Relation::enforceMorphMap([
            'resource' => LearningResource::class,
            'message' => ChatMessage::class,
            'user' => User::class,
            'subject' => Subject::class,
            'report' => Report::class,
            'offer' => Offer::class,
        ]);
    }
}
