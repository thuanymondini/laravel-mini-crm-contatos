<?php

namespace Tests\Unit\Application;

use App\Application\Contact\UseCases\ListContactsUseCase;
use App\Domain\Contact\Contracts\ContactRepositoryInterface;
use App\Domain\Contact\Entities\Contact;
use App\Domain\Contact\ValueObjects\Email;
use App\Domain\Contact\ValueObjects\Phone;
use PHPUnit\Framework\TestCase;

class ListContactsUseCaseTest extends TestCase
{
    /** @test */
    public function test_returns_paginated_contacts(): void
    {
        $contacts = [
            new Contact(id: 1, name: 'João', email: new Email('joao@test.com'), phone: new Phone('11999999999')),
            new Contact(id: 2, name: 'Maria', email: new Email('maria@test.com'), phone: new Phone('21988887777')),
        ];

        $repository = $this->createMock(ContactRepositoryInterface::class);
        $repository->expects($this->once())
            ->method('findAll')
            ->with(1, 15)
            ->willReturn(['data' => $contacts, 'paginator' => null]);

        $useCase = new ListContactsUseCase($repository);
        $result = $useCase->execute(1, 15);

        $this->assertCount(2, $result['data']);
        $this->assertEquals('João', $result['data'][0]->getName());
    }
}
