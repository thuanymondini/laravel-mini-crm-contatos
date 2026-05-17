<?php

namespace App\Infrastructure\Contact\Providers;

use App\Domain\Contact\Contracts\ContactRepositoryInterface;
use App\Infrastructure\Contact\Eloquent\EloquentContactRepository;
use Illuminate\Support\ServiceProvider;

class ContactServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            ContactRepositoryInterface::class,
            EloquentContactRepository::class
        );
    }
}
