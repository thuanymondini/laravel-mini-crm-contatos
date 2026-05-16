<?php

namespace App\Domain\Contact\Entities;

use App\Domain\Contact\ValueObjects\Email;
use App\Domain\Contact\ValueObjects\Phone;
use App\Domain\Contact\ValueObjects\ContactStatus;
use DateTimeImmutable;
use DomainException;

class Contact
{
    private ContactStatus $status;
    private int $score;
    private ?DateTimeImmutable $processedAt;

    public function __construct(
        private readonly ?int $id,
        private string $name,
        private Email $email,
        private Phone $phone,
        ContactStatus $status = ContactStatus::Pending,
        int $score = 0,
        ?DateTimeImmutable $processedAt = null,
    ) {
        $this->status = $status;
        $this->score = $score;
        $this->processedAt = $processedAt;
    }

    public function startProcessing(): void
    {
        if ($this->status !== ContactStatus::Pending) {
            throw new DomainException("Só é possível processar contatos com status 'pending'. Status atual: {$this->status->value}");
        }

        $this->status = ContactStatus::Processing;
    }

    public function markAsActive(int $score): void
    {
        if ($this->status !== ContactStatus::Processing) {
            throw new DomainException("Só é possível ativar contatos com status 'processing'. Status atual: {$this->status->value}");
        }

        $this->score = $score;
        $this->status = ContactStatus::Active;
        $this->processedAt = new DateTimeImmutable();
    }

    public function markAsFailed(): void
    {
        if ($this->status !== ContactStatus::Processing) {
            throw new DomainException("Só é possível falhar contatos com status 'processing'. Status atual: {$this->status->value}");
        }

        $this->status = ContactStatus::Failed;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getEmail(): Email
    {
        return $this->email;
    }

    public function getPhone(): Phone
    {
        return $this->phone;
    }

    public function getStatus(): ContactStatus
    {
        return $this->status;
    }

    public function getScore(): int
    {
        return $this->score;
    }

    public function getProcessedAt(): ?DateTimeImmutable
    {
        return $this->processedAt;
    }
}
