<?php

namespace App\Domain\Contact\Events;

class ContactScoreProcessed
{
    public function __construct(
        public readonly int $contactId,
        public readonly string $email,
        public readonly int $score,
        public readonly string $status,
    ) {}
}
