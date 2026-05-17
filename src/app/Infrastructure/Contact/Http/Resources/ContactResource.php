<?php

namespace App\Infrastructure\Contact\Http\Resources;

use App\Domain\Contact\Entities\Contact;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ContactResource extends JsonResource
{
    /**
     * @param Contact $resource
     */
    public function __construct(private Contact $contact)
    {
        parent::__construct($contact);
    }

    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->contact->getId(),
            'name'         => $this->contact->getName(),
            'email'        => (string) $this->contact->getEmail(),
            'phone'        => (string) $this->contact->getPhone(),
            'score'        => $this->contact->getScore(),
            'status'       => $this->contact->getStatus()->value,
            'processed_at' => $this->contact->getProcessedAt()?->format('Y-m-d\TH:i:s.u\Z'),
        ];
    }
}
