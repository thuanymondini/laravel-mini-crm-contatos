<?php

namespace App\Application\Contact\UseCases;

use App\Domain\Contact\Contracts\ContactRepositoryInterface;
use App\Domain\Contact\Services\ScoreCalculatorService;

class ProcessContactScoreUseCase
{
    public function __construct(
        private ContactRepositoryInterface $repository,
        private ScoreCalculatorService $calculator,
    ) {}

    public function execute(int $contactId): void
    {
        $contact = $this->repository->find($contactId);
        $contact->startProcessing();
        $this->repository->save($contact);

        try {
            $score = $this->calculator->calculate($contact);
            $contact->markAsActive($score);
        } catch (\Throwable $e) {
            $contact->markAsFailed();
            $this->repository->save($contact);
            throw $e;
        }

        $this->repository->save($contact);
    }
}
