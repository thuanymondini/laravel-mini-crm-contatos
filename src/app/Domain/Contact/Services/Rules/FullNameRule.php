<?php

namespace App\Domain\Contact\Services\Rules;

use App\Domain\Contact\Contracts\ScoreRuleInterface;
use App\Domain\Contact\Entities\Contact;

class FullNameRule implements ScoreRuleInterface
{
    public function calculate(Contact $contact): int
    {
        $name = $contact->getName();
        $score = 0;

        //limpa espaços extras antes e depois do nome
        $name = trim($name);

        //verifica quantos espaços existem no nome, se tiver mais de um, é considerado um nome completo
        if (substr_count($name, ' ') >= 1) {
            $score += 10;
        }

        return $score;
    }
}
