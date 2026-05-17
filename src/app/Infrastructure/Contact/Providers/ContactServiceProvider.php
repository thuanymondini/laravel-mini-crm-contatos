<?php

namespace App\Infrastructure\Contact\Providers;

use App\Domain\Contact\Contracts\ContactRepositoryInterface;
use App\Infrastructure\Contact\Eloquent\EloquentContactRepository;
use Illuminate\Support\ServiceProvider;

use App\Domain\Contact\Services\ScoreCalculatorService;
use App\Domain\Contact\Services\Rules\EmailDomainRule;
use App\Domain\Contact\Services\Rules\FullNameRule;
use App\Domain\Contact\Services\Rules\PhoneDDDRule;

class ContactServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            ContactRepositoryInterface::class,
            EloquentContactRepository::class
        );

        $this->app->bind(ScoreCalculatorService::class, function () {
        return new ScoreCalculatorService([
            new EmailDomainRule(),
            new FullNameRule(),
            new PhoneDDDRule(),
        ]);
    });
    }
}
