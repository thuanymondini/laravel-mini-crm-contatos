<?php

namespace App\Domain\Contact\Services\Rules;

use App\Domain\Contact\Contracts\ScoreRuleInterface;
use App\Domain\Contact\Entities\Contact;

class PhoneDDDRule implements ScoreRuleInterface
{
    public function calculate(Contact $contact): int
    {
        $score = 0;
        $phone = $contact->getPhone();

        if ($phone->isFromSaoPaulo()) {
            $score += 20;
        } else {
            $score += 10;
        }

        return $score;
    }
}
