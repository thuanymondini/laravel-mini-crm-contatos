<?php

namespace Tests\Unit\Application;

use App\Application\Contact\UseCases\UpdateContactUseCase;
use App\Domain\Contact\Contracts\ContactRepositoryInterface;
use App\Domain\Contact\Entities\Contact;
use App\Domain\Contact\ValueObjects\Email;
use App\Domain\Contact\ValueObjects\Phone;
use App\Domain\Contact\ValueObjects\ContactStatus;
use PHPUnit\Framework\TestCase;

class UpdateContactUseCaseTest extends TestCase
{
    /** @test */
    public function test_updates_a_contact(): void
    {
        $existing = new Contact(
            id: 1,
            name: 'João Silva',
            email: new Email('joao@empresa.com.br'),
            phone: new Phone('11999999999'),
            status: ContactStatus::Pending,
            score: 0,
        );

        $repository = $this->createMock(ContactRepositoryInterface::class);

        $repository->expects($this->once())
            ->method('find')
            ->with(1)
            ->willReturn($existing);

        $repository->expects($this->once())
            ->method('save')
            ->with($this->callback(function (Contact $contact) {
                return $contact->getName() === 'Maria Souza'
                    && (string) $contact->getEmail() === 'maria@nova.com'
                    && (string) $contact->getPhone() === '21988887777'
                    && $contact->getStatus() === ContactStatus::Pending
                    && $contact->getScore() === 0;
            }));

        $useCase = new UpdateContactUseCase($repository);
        $useCase->execute(1, 'Maria Souza', 'maria@nova.com', '21988887777');
    }

    /** @test */
    public function test_preserves_status_and_score_on_update(): void
    {
        $existing = new Contact(
            id: 1,
            name: 'João',
            email: new Email('joao@test.com'),
            phone: new Phone('11999999999'),
            status: ContactStatus::Active,
            score: 50,
        );

        $repository = $this->createMock(ContactRepositoryInterface::class);
        $repository->method('find')->willReturn($existing);

        $repository->expects($this->once())
            ->method('save')
            ->with($this->callback(function (Contact $contact) {
                return $contact->getStatus() === ContactStatus::Active
                    && $contact->getScore() === 50;
            }));

        $useCase = new UpdateContactUseCase($repository);
        $useCase->execute(1, 'João Atualizado', 'joao@test.com', '11999999999');
    }
}
