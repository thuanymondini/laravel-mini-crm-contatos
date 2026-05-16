<?php

namespace Tests\Unit\Domain\ValueObjects;

use PHPUnit\Framework\TestCase;
use App\Domain\Contact\ValueObjects\ContactStatus;

class ContactStatusTest extends TestCase
{
    public function test_all_expected_statuses_exist(): void
    {
        $this->assertEquals('pending', ContactStatus::Pending->value);
        $this->assertEquals('processing', ContactStatus::Processing->value);
        $this->assertEquals('active', ContactStatus::Active->value);
        $this->assertEquals('failed', ContactStatus::Failed->value);
    }

    public function test_can_create_from_valid_string(): void
    {
        $status = ContactStatus::from('active');
        $this->assertEquals(ContactStatus::Active, $status);
    }

    public function test_throws_exception_for_invalid_string(): void
    {
        $this->expectException(\ValueError::class);
        ContactStatus::from('invalid');
    }

    public function test_tryFrom_returns_null_for_invalid_string(): void
    {
        $this->assertNull(ContactStatus::tryFrom('invalid'));
    }
}
