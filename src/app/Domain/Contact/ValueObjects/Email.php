<?php
namespace App\Domain\Contact\ValueObjects;

class Email
{
    private const NON_CORPORATE = ['gmail.com', 'hotmail.com', 'yahoo.com', 'outlook.com'];

    public function __construct(private readonly string $value)
    {
        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException("Email inválido: {$value}");
        }
    }

    public function isCorporate(): bool
    {
        $domain = explode('@', $this->value)[1];
        return !in_array($domain, self::NON_CORPORATE);
    }

    public function isBrazilian(): bool
    {
        return str_ends_with($this->value, '.br');
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
