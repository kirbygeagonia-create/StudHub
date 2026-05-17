<?php

namespace App\Providers;

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
        \Illuminate\Database\Eloquent\Relations\Relation::enforceMorphMap([
            'resource' => \App\Models\LearningResource::class,
            'message' => \App\Models\ChatMessage::class,
            'user' => \App\Models\User::class,
            'subject' => \App\Models\Subject::class,
            'report' => \App\Models\Report::class,
            'offer' => \App\Models\Offer::class,
        ]);
    }
}
