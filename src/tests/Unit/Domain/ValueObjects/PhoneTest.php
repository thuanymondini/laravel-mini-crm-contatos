<?php

namespace Tests\Unit\Domain\ValueObjects;

use PHPUnit\Framework\TestCase;
use App\Domain\Contact\ValueObjects\Phone;

class PhoneTest extends TestCase
{
    public function test_throws_exception_for_invalid_phone(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new Phone('invalid');
    }

    public function test_accepts_plain_phone(): void
    {
        $phone = new Phone('11999999999');
        $this->assertEquals('11999999999', (string) $phone);
    }

    public function test_accepts_phone_without_ninth_digit(): void
    {
        $phone = new Phone('1199999999');
        $this->assertEquals('11999999999', (string) $phone);
    }

    public function test_strips_ddi_with_plus_sign(): void
    {
        $phone = new Phone('+5511999999999');
        $this->assertEquals('11999999999', (string) $phone);
    }

    public function test_strips_ddi_without_plus_sign(): void
    {
        $phone = new Phone('5511999999999');
        $this->assertEquals('11999999999', (string) $phone);
    }

    public function test_strips_dashes(): void
    {
        $phone = new Phone('11-99999-9999');
        $this->assertEquals('11999999999', (string) $phone);
    }

    public function test_strips_parentheses(): void
    {
        $phone = new Phone('(11) 99999-9999');
        $this->assertEquals('11999999999', (string) $phone);
    }

    public function test_strips_spaces(): void
    {
        $phone = new Phone('11 99999 9999');
        $this->assertEquals('11999999999', (string) $phone);
    }

    public function test_strips_all_formatting(): void
    {
        $phone = new Phone('+55 (11) 99999-9999');
        $this->assertEquals('11999999999', (string) $phone);
    }

    public function test_area_code_from_sao_paulo(): void
    {
        $phone_11 = new Phone('11999999999');
        $phone_12 = new Phone('12999999999');
        $phone_13 = new Phone('13999999999');
        $phone_14 = new Phone('14999999999');
        $phone_15 = new Phone('15999999999');
        $phone_16 = new Phone('16999999999');
        $phone_17 = new Phone('17999999999');
        $phone_18 = new Phone('18999999999');
        $phone_19 = new Phone('19999999999');

        $this->assertTrue($phone_11->isFromSaoPaulo());
        $this->assertTrue($phone_12->isFromSaoPaulo());
        $this->assertTrue($phone_13->isFromSaoPaulo());
        $this->assertTrue($phone_14->isFromSaoPaulo());
        $this->assertTrue($phone_15->isFromSaoPaulo());
        $this->assertTrue($phone_16->isFromSaoPaulo());
        $this->assertTrue($phone_17->isFromSaoPaulo());
        $this->assertTrue($phone_18->isFromSaoPaulo());
        $this->assertTrue($phone_19->isFromSaoPaulo());
    }

    public function test_area_code_not_from_sao_paulo(): void
    {
        $phone = new Phone('21999999999');
        $this->assertFalse($phone->isFromSaoPaulo());
    }
}
