<?php

namespace Tests\Feature;

use App\Domain\Contact\Events\ContactScoreProcessed;
use App\Infrastructure\Contact\Listeners\LogContactScoreListener;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class ContactScoreListenerTest extends TestCase
{
    /** @test */
    public function test_listener_writes_to_contact_log_channel(): void
    {
        Log::shouldReceive('channel')
            ->with('contact')
            ->andReturnSelf();

        Log::shouldReceive('info')
            ->once()
            ->withArgs(function (string $message, array $context) {
                return $message === 'Contact score processed'
                    && $context['id'] === 1
                    && $context['email'] === 'joao@empresa.com.br'
                    && $context['score'] === 60
                    && $context['status'] === 'active';
            });

        $event = new ContactScoreProcessed(
            contactId: 1,
            email: 'joao@empresa.com.br',
            score: 60,
            status: 'active'
        );

        $listener = new LogContactScoreListener();
        $listener->handle($event);
    }

    /** @test */
    public function test_listener_is_registered_for_contact_score_processed_event(): void
    {
        $event = new ContactScoreProcessed(
            contactId: 1,
            email: 'joao@empresa.com.br',
            score: 60,
            status: 'active'
        );

        Log::shouldReceive('channel')->with('contact')->andReturnSelf();
        Log::shouldReceive('info')->once();

        event($event);
    }
}
