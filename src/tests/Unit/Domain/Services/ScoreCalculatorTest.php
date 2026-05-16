<?php

namespace Tests\Unit\Domain\Services;

use App\Domain\Contact\Entities\Contact;
use App\Domain\Contact\ValueObjects\Email;
use App\Domain\Contact\ValueObjects\Phone;
use App\Domain\Contact\Services\ScoreCalculatorService;
use App\Domain\Contact\Services\Rules\EmailDomainRule;
use App\Domain\Contact\Services\Rules\FullNameRule;
use App\Domain\Contact\Services\Rules\PhoneDDDRule;
use App\Domain\Contact\Contracts\ScoreRuleInterface;
use PHPUnit\Framework\TestCase;

class ScoreCalculatorTest extends TestCase
{
    private function makeContact(string $name, string $email, string $phone): Contact
    {
        return new Contact(
            id: null,
            name: $name,
            email: new Email($email),
            phone: new Phone($phone),
        );
    }

    /** @test */
    public function test_returns_zero_when_no_rules_apply(): void
    {
        $contact = $this->makeContact('João', 'joao@gmail.com', '99999999999');
        $calculator = new ScoreCalculatorService([
            new EmailDomainRule(),
            new FullNameRule(),
        ]);

        $score = $calculator->calculate($contact);

        $this->assertEquals(0, $score);
    }

    /** @test */
    public function test_adds_20_for_corporate_email(): void
    {
        $contact = $this->makeContact('João', 'joao@empresa.com', '99999999999');
        $calculator = new ScoreCalculatorService([new EmailDomainRule()]);

        $this->assertEquals(20, $calculator->calculate($contact));
    }

    /** @test */
    public function test_adds_10_for_brazilian_email(): void
    {
        $contact = $this->makeContact('João', 'joao@gmail.com.br', '99999999999');
        $calculator = new ScoreCalculatorService([new EmailDomainRule()]);

        $this->assertEquals(10, $calculator->calculate($contact));
    }

    /** @test */
    public function test_adds_30_for_corporate_brazilian_email(): void
    {
        $contact = $this->makeContact('João', 'joao@empresa.com.br', '99999999999');
        $calculator = new ScoreCalculatorService([new EmailDomainRule()]);

        $this->assertEquals(30, $calculator->calculate($contact));
    }

    /** @test */
    public function test_adds_10_for_full_name(): void
    {
        $contact = $this->makeContact('João Silva', 'joao@gmail.com', '99999999999');
        $calculator = new ScoreCalculatorService([new FullNameRule()]);

        $this->assertEquals(10, $calculator->calculate($contact));
    }

    /** @test */
    public function test_adds_nothing_for_single_name(): void
    {
        $contact = $this->makeContact('João', 'joao@gmail.com', '99999999999');
        $calculator = new ScoreCalculatorService([new FullNameRule()]);

        $this->assertEquals(0, $calculator->calculate($contact));
    }

    /** @test */
    public function test_adds_20_for_sao_paulo_ddd(): void
    {
        $contact = $this->makeContact('João', 'joao@gmail.com', '11999999999');
        $calculator = new ScoreCalculatorService([new PhoneDDDRule()]);

        $this->assertEquals(20, $calculator->calculate($contact));
    }

    /** @test */
    public function test_adds_10_for_other_state_ddd(): void
    {
        $contact = $this->makeContact('João', 'joao@gmail.com', '21999999999');
        $calculator = new ScoreCalculatorService([new PhoneDDDRule()]);

        $this->assertEquals(10, $calculator->calculate($contact));
    }

    /** @test */
    public function test_sums_all_rules(): void
    {
        // corporativo +20, .br +10, nome completo +10, SP DDD +20 = 60
        $contact = $this->makeContact('João Silva', 'joao@empresa.com.br', '11999999999');
        $calculator = new ScoreCalculatorService([
            new EmailDomainRule(),
            new FullNameRule(),
            new PhoneDDDRule(),
        ]);

        $this->assertEquals(60, $calculator->calculate($contact));
    }

    /** @test */
    public function test_accepts_custom_rules_via_strategy(): void
    {
        $customRule = $this->createMock(ScoreRuleInterface::class);
        $customRule->method('calculate')->willReturn(99);

        $contact = $this->makeContact('João', 'joao@gmail.com', '99999999999');
        $calculator = new ScoreCalculatorService([$customRule]);

        $this->assertEquals(99, $calculator->calculate($contact));
    }
}
