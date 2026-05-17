<?php

namespace App\Application\Contact\UseCases;

use App\Domain\Contact\Contracts\ContactRepositoryInterface;

class DeleteContactUseCase
{
    public function __construct(
        private ContactRepositoryInterface $repository
    ) {}

    public function execute(int $id): void
    {
        $this->repository->delete($id);
    }
}
