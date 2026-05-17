<?php

namespace App\Application\Contact\UseCases;

use App\Domain\Contact\Contracts\ContactRepositoryInterface;
use App\Domain\Contact\Entities\Contact;
use App\Domain\Contact\ValueObjects\Email;
use App\Domain\Contact\ValueObjects\Phone;

class UpdateContactUseCase
{
    public function __construct(
        private ContactRepositoryInterface $repository
    ) {}

    public function execute(int $id, string $name, string $email, string $phone): Contact
    {
        $contact = $this->repository->find($id);

        $updated = new Contact(
            id: $contact->getId(),
            name: $name,
            email: new Email($email),
            phone: new Phone($phone),
            status: $contact->getStatus(),
            score: $contact->getScore(),
            processedAt: $contact->getProcessedAt(),
        );

        $this->repository->save($updated);

        return $updated;
    }
}
