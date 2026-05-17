<?php

namespace Tests\Unit\Application;

use App\Application\Contact\UseCases\DeleteContactUseCase;
use App\Domain\Contact\Contracts\ContactRepositoryInterface;
use PHPUnit\Framework\TestCase;

class DeleteContactUseCaseTest extends TestCase
{
    /** @test */
    public function test_deletes_a_contact_by_id(): void
    {
        $repository = $this->createMock(ContactRepositoryInterface::class);
        $repository->expects($this->once())
            ->method('delete')
            ->with(1);

        $useCase = new DeleteContactUseCase($repository);
        $useCase->execute(1);
    }
}
