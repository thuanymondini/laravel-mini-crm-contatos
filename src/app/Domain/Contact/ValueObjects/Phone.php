<?php

namespace App\Domain\Contact\ValueObjects;

class Phone
{
    public readonly int $ddd;
    public readonly string $number;

    public function __construct(private string $value)
    {
        $original = $value;

        // detecta presença de DDI pelo sinal de +
        $hasDDI = str_contains($value, '+');

        // valida caracteres permitidos (dígitos, espaços, parênteses, traços, +)
        if (!preg_match('/^[\d\s\-\+\(\)]+$/', $value)) {
            throw new \InvalidArgumentException("Telefone inválido: {$original}");
        }

        // remove formatação
        $this->value = preg_replace('/[\s\-\+\(\)]/', '', $this->value);

        // com +: valida DDI brasileiro e remove
        if ($hasDDI) {
            if (!str_starts_with($this->value, '55')) {
                throw new \InvalidArgumentException("Apenas números brasileiros são aceitos: {$original}");
            }
            $this->value = substr($this->value, 2);
        }

        // sem +, mas com 12 ou 13 dígitos: assume DDI implícito
        if (!$hasDDI && (strlen($this->value) === 12 || strlen($this->value) === 13)) {
            if (!str_starts_with($this->value, '55')) {
                throw new \InvalidArgumentException("Apenas números brasileiros são aceitos: {$original}");
            }
            $this->value = substr($this->value, 2);
        }

        // valida se restam 10 ou 11 dígitos (DDD + número)
        if (strlen($this->value) !== 10 && strlen($this->value) !== 11) {
            throw new \InvalidArgumentException("Telefone deve conter 10 ou 11 dígitos (DDD + número): {$original}");
        }

        // celular com 11 dígitos deve ter 9 como terceiro dígito (nono dígito)
        if (strlen($this->value) === 11 && $this->value[2] !== '9') {
            throw new \InvalidArgumentException("Telefone celular deve começar com 9 após o DDD: {$original}");
        }

        // valida DDD brasileiro (11 a 99)
        $dddValue = (int) substr($this->value, 0, 2);
        if ($dddValue < 11 || $dddValue > 99) {
            throw new \InvalidArgumentException("DDD inválido: {$original} (deve ser entre 11 e 99)");
        }

        // normaliza fixo (10 dígitos) adicionando o 9 após o DDD
        if (strlen($this->value) === 10) {
            $this->value = substr_replace($this->value, '9', 2, 0);
        }

        $this->ddd = $dddValue;
        $this->number = substr($this->value, 2);
    }

    // DDD de São Paulo: 11 a 19
    public function isFromSaoPaulo(): bool
    {
        return $this->ddd >= 11 && $this->ddd <= 19;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
