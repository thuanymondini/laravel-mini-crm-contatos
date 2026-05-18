<?php

namespace Tests\Feature;

use App\Domain\Contact\Events\ContactScoreProcessed;
use App\Infrastructure\Contact\Eloquent\ContactModel;
use App\Infrastructure\Contact\Jobs\ProcessContactScoreJob;
use App\Application\Contact\UseCases\ProcessContactScoreUseCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class ContactBroadcastTest extends TestCase
{
    use RefreshDatabase;

    public function test_contact_score_processed_event_is_dispatched_after_processing(): void
    {
        Event::fake([ContactScoreProcessed::class]);

        $contact = ContactModel::factory()->create([
            'name'  => 'João Silva',
            'email' => 'joao@empresa.com.br',
            'phone' => '11999999999',
        ]);

        $job = new ProcessContactScoreJob($contact->id);
        $job->handleForTesting(app(ProcessContactScoreUseCase::class));

        Event::assertDispatched(ContactScoreProcessed::class, function ($event) use ($contact) {
            return $event->contactId === $contact->id
                && $event->score === 60
                && $event->status === 'active';
        });
    }

    public function test_contact_score_processed_event_broadcasts_on_correct_channel(): void
    {
        $event = new ContactScoreProcessed(
            contactId: 5,
            email: 'test@test.com',
            score: 40,
            status: 'active'
        );

        $this->assertEquals('contacts.5', $event->broadcastOn()->name);
        $this->assertEquals('ContactScoreProcessed', $event->broadcastAs());
    }
}
