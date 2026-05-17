<?php

namespace App\Application\Contact\UseCases;


use App\Domain\Contact\Contracts\ContactRepositoryInterface;
use App\Domain\Contact\Entities\Contact;
use App\Domain\Contact\ValueObjects\Email;
use App\Domain\Contact\ValueObjects\Phone;

class CreateContactUseCase
{
    public function __construct(private ContactRepositoryInterface $repository) {}

    public function execute(string $name, string $email, string $phone): Contact
    {
        $contact = new Contact(
            id: null,
            name: $name,
            email: new Email($email),
            phone: new Phone($phone),
        );

        $this->repository->save($contact);

        return $contact;
    }
}
