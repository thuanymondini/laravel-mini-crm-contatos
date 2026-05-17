<?php

namespace App\Application\Contact\UseCases;

use App\Domain\Contact\Contracts\ContactRepositoryInterface;
use App\Domain\Contact\Services\ScoreCalculatorService;
use App\Domain\Contact\Events\ContactScoreProcessed;
use \Illuminate\Contracts\Events\Dispatcher;

class ProcessContactScoreUseCase
{
    public function __construct(
        private ContactRepositoryInterface $repository,
        private ScoreCalculatorService $calculator,
        private Dispatcher $dispatcher,
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

            $this->dispatcher->dispatch(new ContactScoreProcessed(
                contactId: $contact->getId(),
                email: (string) $contact->getEmail(),
                score: $contact->getScore(),
                status: $contact->getStatus()->value,
            ));

            throw $e;
        }

        $this->repository->save($contact);

        $this->dispatcher->dispatch(new ContactScoreProcessed(
            contactId: $contact->getId(),
            email: (string) $contact->getEmail(),
            score: $contact->getScore(),
            status: $contact->getStatus()->value,
        ));
    }
}
