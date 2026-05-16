<?php

namespace Tests\Unit\Domain\ValueObjects;

use PHPUnit\Framework\TestCase;
use App\Domain\Contact\ValueObjects\Email;

class EmailTest extends TestCase
{
    public function test_throws_exception_for_invalid_email(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new Email('invalid');
    }

    public function test_identifies_corporate_domain(): void
    {
        $email = new Email('joao@empresa.com.br');
        $this->assertTrue($email->isCorporate());
    }

    public function test_identifies_non_corporate_domain(): void
    {
        $email = new Email('joao@gmail.com');
        $this->assertFalse($email->isCorporate());
    }

    public function test_identifies_brazilian_domain(): void
    {
        $email = new Email('joao@empresa.com.br');
        $this->assertTrue($email->isBrazilian());
    }

    public function test_identifies_non_brazilian_domain(): void
    {
        $email = new Email('joao@company.com');
        $this->assertFalse($email->isBrazilian());
    }
}
