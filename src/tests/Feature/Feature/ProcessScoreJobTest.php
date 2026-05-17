<?php

namespace Tests\Feature;

use App\Application\Contact\UseCases\ProcessContactScoreUseCase;
use App\Domain\Contact\Events\ContactScoreProcessed;
use App\Infrastructure\Contact\Eloquent\ContactModel;
use App\Infrastructure\Contact\Jobs\ProcessContactScoreJob;
use App\Infrastructure\Contact\Listeners\LogContactScoreListener;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class ProcessScoreJobTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function test_process_score_endpoint_dispatches_job_and_returns_202(): void
    {
        Queue::fake();

        $contact = ContactModel::factory()->create();

        $response = $this->postJson("/api/contacts/{$contact->id}/process-score");

        $response->assertStatus(202);
        Queue::assertPushed(ProcessContactScoreJob::class, function ($job) use ($contact) {
            return $job->contactId === $contact->id;
        });
    }

    /** @test */
    public function test_process_score_returns_404_for_nonexistent_contact(): void
    {
        $response = $this->postJson('/api/contacts/999/process-score');

        $response->assertStatus(404);
    }

    /** @test */
    public function test_job_processes_contact_to_active_with_score(): void
    {
        Event::fake();

        $contact = ContactModel::factory()->create([
            'name'   => 'João Silva',
            'email'  => 'joao@empresa.com.br',
            'phone'  => '11999999999',
            'status' => 'pending',
        ]);

        $job = new ProcessContactScoreJob($contact->id);
        $job->handleForTesting(app(ProcessContactScoreUseCase::class));

        $contact->refresh();

        $this->assertEquals('active', $contact->status);
        $this->assertGreaterThan(0, $contact->score);
        $this->assertNotNull($contact->processed_at);
    }

    /** @test */
    public function test_job_calculates_max_score_corporate_br_full_name_sp_ddd(): void
    {
        Event::fake();

        $contact = ContactModel::factory()->create([
            'name'   => 'João Silva',
            'email'  => 'joao@empresa.com.br',
            'phone'  => '11999999999',
            'status' => 'pending',
        ]);

        $job = new ProcessContactScoreJob($contact->id);
        $job->handleForTesting(app(ProcessContactScoreUseCase::class));

        $contact->refresh();
        $this->assertEquals(60, $contact->score);
    }

    /** @test */
    public function test_job_calculates_min_score_gmail_single_name_non_sp_ddd(): void
    {
        Event::fake();

        $contact = ContactModel::factory()->create([
            'name'   => 'Maria',
            'email'  => 'maria@gmail.com',
            'phone'  => '21999999999',
            'status' => 'pending',
        ]);

        $job = new ProcessContactScoreJob($contact->id);
        $job->handleForTesting(app(ProcessContactScoreUseCase::class));

        $contact->refresh();
        $this->assertEquals(10, $contact->score);
    }

    /** @test */
    public function test_job_dispatches_contact_score_processed_event(): void
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
            return $event->contactId === $contact->id;
        });
    }

    /** @test */
    public function test_contact_is_marked_as_failed_when_score_calculation_throws(): void
    {
        Event::fake();

        $contact = ContactModel::factory()->create([
            'name'   => 'João Silva',
            'email'  => 'joao@empresa.com.br',
            'phone'  => '11999999999',
            'status' => 'pending',
        ]);

        $this->mock(\App\Domain\Contact\Services\ScoreCalculatorService::class)
            ->shouldReceive('calculate')
            ->andThrow(new \RuntimeException('Calculation failed'));

        $job = new ProcessContactScoreJob($contact->id);

        try {
            $job->handleForTesting(app(ProcessContactScoreUseCase::class));
        } catch (\RuntimeException) {
        }

        $contact->refresh();
        $this->assertEquals('failed', $contact->status);
    }

    /** @test */
    public function test_listener_writes_to_contact_log_channel(): void
    {
        Log::shouldReceive('channel')
            ->with('contact')
            ->andReturnSelf();

        Log::shouldReceive('info')
            ->once()
            ->withArgs(function (string $message, array $context) {
                return $context['id'] === 1
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
}
