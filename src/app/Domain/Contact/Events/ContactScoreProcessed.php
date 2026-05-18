<?php

namespace App\Domain\Contact\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;


class ContactScoreProcessed implements ShouldBroadcast
{
    public function __construct(
        public readonly int $contactId,
        public readonly string $email,
        public readonly int $score,
        public readonly string $status,
    ) {}

    public function broadcastOn(): Channel
    {
        return new Channel("contacts.{$this->contactId}");
    }

    public function broadcastAs(): string
    {
        return 'ContactScoreProcessed';
    }
}
