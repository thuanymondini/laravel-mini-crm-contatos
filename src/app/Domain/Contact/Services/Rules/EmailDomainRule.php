<?php

namespace App\Domain\Contact\Services\Rules;

use App\Domain\Contact\Contracts\ScoreRuleInterface;
use App\Domain\Contact\Entities\Contact;

class EmailDomainRule implements ScoreRuleInterface
{
    public function calculate(Contact $contact): int
    {
        $score = 0;
        $email = $contact->getEmail();

        if ($email->isCorporate()) {
            $score += 20;
        }

        if ($email->isBrazilian()) {
            $score += 10;
        }

        return $score;
    }
}
