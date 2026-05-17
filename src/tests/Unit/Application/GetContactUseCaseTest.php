<?php

namespace Tests\Unit\Application;

use App\Application\Contact\UseCases\GetContactUseCase;
use App\Application\Contact\UseCases\ListContactsUseCase;
use App\Application\Contact\UseCases\UpdateContactUseCase;
use App\Application\Contact\UseCases\DeleteContactUseCase;
use App\Domain\Contact\Contracts\ContactRepositoryInterface;
use App\Domain\Contact\Entities\Contact;
use App\Domain\Contact\ValueObjects\Email;
use App\Domain\Contact\ValueObjects\Phone;
use App\Domain\Contact\ValueObjects\ContactStatus;
use PHPUnit\Framework\TestCase;

class GetContactUseCaseTest extends TestCase
{
    /** @test */
    public function test_returns_a_contact_by_id(): void
    {
        $contact = new Contact(
            id: 1,
            name: 'João Silva',
            email: new Email('joao@empresa.com.br'),
            phone: new Phone('11999999999'),
        );

        $repository = $this->createMock(ContactRepositoryInterface::class);
        $repository->expects($this->once())
            ->method('find')
            ->with(1)
            ->willReturn($contact);

        $useCase = new GetContactUseCase($repository);
        $result = $useCase->execute(1);

        $this->assertSame($contact, $result);
    }
}
