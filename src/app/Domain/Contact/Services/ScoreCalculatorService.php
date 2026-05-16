<?php

namespace App\Domain\Contact\Services;

use App\Domain\Contact\Contracts\ScoreRuleInterface;
use App\Domain\Contact\Entities\Contact;

class ScoreCalculatorService {

    /**
     * Summary of __construct
     * @param ScoreRuleInterface[] $rules
     */
    public function __construct(private array $rules) {}
    public function calculate(Contact $contact): int
    {
        $score = 0;
        foreach ($this->rules as $rule) {
            $score += $rule->calculate($contact);
        }
        return $score;
    }
}
