<?php
namespace App\Domain\Contact\ValueObjects;

class Email
{
    private const NON_CORPORATE = ['gmail', 'hotmail', 'yahoo', 'outlook'];

    public function __construct(private readonly string $value)
    {
        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException("Email inválido: {$value}");
        }
    }

    public function isCorporate(): bool
    {
        $domain = explode('@', $this->value)[1];
        $parts = explode('.', $domain);

        // Pega a parte principal (ex: "gmail" de "gmail.com" ou "gmail.com.br")
        $mainDomain = $parts[0];

        return !in_array($mainDomain, self::NON_CORPORATE);
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
