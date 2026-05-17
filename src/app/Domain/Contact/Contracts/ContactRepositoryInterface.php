<?php

namespace App\Domain\Contact\Contracts;

use App\Domain\Contact\Entities\Contact;

interface ContactRepositoryInterface
{
    public function find(int $id): Contact;
    public function findAll(int $page, int $perPage): array;
    public function save(Contact $contact): void;
    public function delete(int $id): void;
}
