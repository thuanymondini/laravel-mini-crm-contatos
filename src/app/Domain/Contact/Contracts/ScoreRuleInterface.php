<?php

namespace App\Domain\Contact\Contracts;

use App\Domain\Contact\Entities\Contact;
interface ScoreRuleInterface
{
    public function calculate(Contact $contact): int;
}
