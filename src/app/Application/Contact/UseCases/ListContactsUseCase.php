<?php

namespace App\Application\Contact\UseCases;

use App\Domain\Contact\Contracts\ContactRepositoryInterface;

class ListContactsUseCase
{
    public function __construct(
        private ContactRepositoryInterface $repository
    ) {}

    public function execute(int $page = 1, int $perPage = 15): array
    {
        return $this->repository->findAll($page, $perPage);
    }
}
