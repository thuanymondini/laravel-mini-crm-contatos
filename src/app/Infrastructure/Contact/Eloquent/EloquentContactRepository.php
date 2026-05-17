<?php

namespace App\Infrastructure\Contact\Eloquent;

use App\Domain\Contact\Contracts\ContactRepositoryInterface;
use App\Domain\Contact\Entities\Contact;
use App\Domain\Contact\ValueObjects\Email;
use App\Domain\Contact\ValueObjects\Phone;
use App\Domain\Contact\ValueObjects\ContactStatus;

class EloquentContactRepository implements ContactRepositoryInterface
{
    public function find(int $id): Contact
    {
        $model = ContactModel::findOrFail($id);
        return $this->toDomain($model);
    }

    public function findAll(int $page = 1, int $perPage = 15): array
    {
        $paginator = ContactModel::paginate($perPage, ['*'], 'page', $page);

        return [
            'data'      => $paginator->getCollection()->map(fn ($m) => $this->toDomain($m))->all(),
            'paginator' => $paginator,
        ];
    }

    public function save(Contact $contact): void
    {
        ContactModel::updateOrCreate(
            ['id' => $contact->getId()],
            [
                'name'         => $contact->getName(),
                'email'        => (string) $contact->getEmail(),
                'phone'        => (string) $contact->getPhone(),
                'status'       => $contact->getStatus()->value,
                'score'        => $contact->getScore(),
                'processed_at' => $contact->getProcessedAt(),
            ]
        );
    }

    public function delete(int $id): void
    {
        $model = ContactModel::findOrFail($id);
        $model->delete();
    }

    private function toDomain(ContactModel $model): Contact
    {
        return new Contact(
            id: $model->id,
            name: $model->name,
            email: new Email($model->email),
            phone: new Phone($model->phone),
            status: ContactStatus::from($model->status),
            score: $model->score,
            processedAt: $model->processed_at?->toDateTimeImmutable(),
        );
    }
}
