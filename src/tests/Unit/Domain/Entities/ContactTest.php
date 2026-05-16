<?php

namespace Tests\Unit\Domain\Entities;

use App\Domain\Contact\Entities\Contact;
use App\Domain\Contact\ValueObjects\Email;
use App\Domain\Contact\ValueObjects\Phone;
use App\Domain\Contact\ValueObjects\ContactStatus;
use PHPUnit\Framework\TestCase;

class ContactTest extends TestCase
{
    private function makeContact(array $overrides = []): Contact
    {
        return new Contact(
            id: $overrides['id'] ?? null,
            name: $overrides['name'] ?? 'João Silva',
            email: $overrides['email'] ?? new Email('joao@empresa.com.br'),
            phone: $overrides['phone'] ?? new Phone('11999999999'),
        );
    }

    public function test_creates_a_contact_with_pending_status_and_zero_score(): void
    {
        $contact = $this->makeContact();

        $this->assertEquals(ContactStatus::Pending, $contact->getStatus());
        $this->assertEquals(0, $contact->getScore());
        $this->assertNull($contact->getProcessedAt());
    }

    public function test_stores_name_email_and_phone(): void
    {
        $email = new Email('maria@test.com');
        $phone = new Phone('21988887777');

        $contact = $this->makeContact([
            'name' => 'Maria Souza',
            'email' => $email,
            'phone' => $phone,
        ]);

        $this->assertEquals('Maria Souza', $contact->getName());
        $this->assertSame($email, $contact->getEmail());
        $this->assertSame($phone, $contact->getPhone());
    }

    public function test_transitions_to_processing(): void
    {
        $contact = $this->makeContact();

        $contact->startProcessing();

        $this->assertEquals(ContactStatus::Processing, $contact->getStatus());
    }

    public function test_transitions_to_active_with_score_and_processed_at(): void
    {
        $contact = $this->makeContact();
        $contact->startProcessing();

        $contact->markAsActive(50);

        $this->assertEquals(ContactStatus::Active, $contact->getStatus());
        $this->assertEquals(50, $contact->getScore());
        $this->assertNotNull($contact->getProcessedAt());
    }

    public function test_transitions_to_failed(): void
    {
        $contact = $this->makeContact();
        $contact->startProcessing();

        $contact->markAsFailed();

        $this->assertEquals(ContactStatus::Failed, $contact->getStatus());
    }

    public function test_cannot_start_processing_if_not_pending(): void
    {
        $contact = $this->makeContact();
        $contact->startProcessing();
        $contact->markAsActive(30);

        $this->expectException(\DomainException::class);

        $contact->startProcessing();
    }

    public function test_cannot_mark_as_active_if_not_processing(): void
    {
        $contact = $this->makeContact();

        $this->expectException(\DomainException::class);

        $contact->markAsActive(50);
    }

    public function test_cannot_mark_as_failed_if_not_processing(): void
    {
        $contact = $this->makeContact();

        $this->expectException(\DomainException::class);

        $contact->markAsFailed();
    }
}
