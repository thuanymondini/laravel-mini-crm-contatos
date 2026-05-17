<?php

namespace App\Application\Contact\UseCases;

use App\Domain\Contact\Contracts\ContactRepositoryInterface;
use App\Domain\Contact\Entities\Contact;

class GetContactUseCase
{
    public function __construct(
        private ContactRepositoryInterface $repository
    ) {}

    public function execute(int $id): Contact
    {
        return $this->repository->find($id);
    }
}
