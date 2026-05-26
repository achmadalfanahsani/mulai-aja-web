<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Gate;
use App\Models\QuestionPackage;
use App\Models\Question;
use App\Models\QuestionAttempt;
use App\Policies\QuestionPackagePolicy;
use App\Policies\QuestionPolicy;
use App\Policies\QuestionAttemptPolicy;

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
        Paginator::useBootstrapFive();

        Gate::policy(QuestionPackage::class, QuestionPackagePolicy::class);
        Gate::policy(Question::class, QuestionPolicy::class);
        Gate::policy(QuestionAttempt::class, QuestionAttemptPolicy::class);
    }
}
