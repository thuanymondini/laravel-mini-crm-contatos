<?php

namespace Tests\Unit\Application;

use App\Application\Contact\UseCases\CreateContactUseCase;
use App\Domain\Contact\Contracts\ContactRepositoryInterface;
use App\Domain\Contact\Entities\Contact;
use App\Domain\Contact\ValueObjects\ContactStatus;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

class CreateContactUseCaseTest extends TestCase
{
    //aqui é usado intersection types para garantir que a propriedade seja tanto do tipo ContactRepositoryInterface quanto MockObject,
    //permitindo o uso de métodos de mock e garantindo a interface correta
    private ContactRepositoryInterface&MockObject $repository;
    private CreateContactUseCase $useCase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = $this->createMock(ContactRepositoryInterface::class);
        $this->useCase = new CreateContactUseCase($this->repository);
    }

    public function test_create_a_contact_with_pending_status(): void
    {
        $this->repository
            ->expects($this->once())
            ->method('save')
            ->with($this->callback(function (Contact $contact) {
                return $contact->getStatus() === ContactStatus::Pending;
            }));

        $contact = $this->useCase->execute(
            name: 'João Silva',
            email: 'joao@empresa.com.br',
            phone: '11999999999',
        );

        $this->assertEquals(ContactStatus::Pending, $contact->getStatus());
    }

    public function test_create_a_contact_with_zero_score(): void
    {
        $this->repository
            ->expects($this->once())
            ->method('save');

        $contact = $this->useCase->execute(
            name: 'João Silva',
            email: 'joao@empresa.com.br',
            phone: '11999999999',
        );

        $this->assertEquals(0, $contact->getScore());
    }

    public function test_store_correct_name_email_and_phone(): void
    {
        $this->repository
            ->expects($this->once())
            ->method('save');

        $contact = $this->useCase->execute(
            name: 'Maria Souza',
            email: 'maria@test.com',
            phone: '21988887777',
        );

        $this->assertEquals('Maria Souza', $contact->getName());
        $this->assertEquals('maria@test.com', (string) $contact->getEmail());
        $this->assertEquals('21988887777', (string) $contact->getPhone());
    }

    public function test_call_repository_save_exactly_once(): void
    {
        $this->repository
            ->expects($this->once())
            ->method('save');

        $this->useCase->execute(
            name: 'João Silva',
            email: 'joao@empresa.com.br',
            phone: '11999999999',
        );
    }

    public function test_throw_exception_for_invalid_email(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->useCase->execute(
            name: 'João Silva',
            email: 'invalid-email',
            phone: '11999999999',
        );
    }

    public function test_throw_exception_for_invalid_phone(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->useCase->execute(
            name: 'João Silva',
            email: 'joao@empresa.com.br',
            phone: 'invalid-phone',
        );
    }
}
