<?php

namespace Tests\Unit\Application;

use App\Application\Contact\UseCases\ProcessContactScoreUseCase;
use App\Domain\Contact\Contracts\ContactRepositoryInterface;
use App\Domain\Contact\Entities\Contact;

use App\Domain\Contact\Services\ScoreCalculatorService;
use App\Domain\Contact\ValueObjects\ContactStatus;
use App\Domain\Contact\ValueObjects\Email;
use App\Domain\Contact\ValueObjects\Phone;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ProcessContactScoreUseCaseTest extends TestCase
{
    private ContactRepositoryInterface&MockObject $repository;
    private ScoreCalculatorService&MockObject $calculator;
    private ProcessContactScoreUseCase $useCase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = $this->createMock(ContactRepositoryInterface::class);
        $this->calculator = $this->createMock(ScoreCalculatorService::class);
        $this->useCase = new ProcessContactScoreUseCase($this->repository, $this->calculator);
    }

    private function makeContact(): Contact
    {
        return new Contact(
            id: 1,
            name: 'João Silva',
            email: new Email('joao@empresa.com.br'),
            phone: new Phone('11999999999'),
        );
    }

    public function test_change_status_to_processing(): void
    {
        $contact = $this->makeContact();
        $savedStatuses = [];

        $this->repository->method('find')->willReturn($contact);
        $this->calculator->method('calculate')->willReturn(50);
        $this->repository
            ->expects($this->atLeastOnce())
            ->method('save')
            ->willReturnCallback(function (Contact $c) use (&$savedStatuses) {
                $savedStatuses[] = $c->getStatus();
            });

        $this->useCase->execute(1);

        $this->assertEquals(ContactStatus::Processing, $savedStatuses[0]);
    }

    public function test_calculates_and_saves_score(): void
    {
        $contact = $this->makeContact();
        $this->repository->method('find')->willReturn($contact);
        $this->calculator->expects($this->once())->method('calculate')->willReturn(60);
        $this->repository->expects($this->atLeastOnce())->method('save');

        $this->useCase->execute(1);

        $this->assertEquals(60, $contact->getScore());
    }

    public function test_marks_contact_as_active_after_processing(): void
    {
        $contact = $this->makeContact();
        $this->repository->method('find')->willReturn($contact);
        $this->calculator->method('calculate')->willReturn(40);
        $this->repository->expects($this->atLeastOnce())->method('save');

        $this->useCase->execute(1);

        $this->assertEquals(ContactStatus::Active, $contact->getStatus());
    }

    public function test_fills_processed_at_after_processing(): void
    {
        $contact = $this->makeContact();
        $this->repository->method('find')->willReturn($contact);
        $this->calculator->method('calculate')->willReturn(30);
        $this->repository->expects($this->atLeastOnce())->method('save');

        $this->useCase->execute(1);

        $this->assertNotNull($contact->getProcessedAt());
    }

    public function test_calls_repository_find_with_correct_id(): void
    {
        $contact = $this->makeContact();
        $this->repository->expects($this->once())->method('find')->with(1)->willReturn($contact);
        $this->calculator->method('calculate')->willReturn(0);

        $this->useCase->execute(1);
    }

    public function test_marks_contact_as_failed_on_exception(): void
    {
        $contact = $this->makeContact();
        $this->repository->method('find')->willReturn($contact);
        $this->calculator->method('calculate')->willThrowException(new \RuntimeException('erro'));
        $this->repository->expects($this->atLeastOnce())->method('save');

        try {
            $this->useCase->execute(1);
        } catch (\RuntimeException) {
            // esperado
        }

        $this->assertEquals(ContactStatus::Failed, $contact->getStatus());
    }
}
