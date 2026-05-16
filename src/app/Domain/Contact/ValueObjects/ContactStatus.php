<?php

namespace App\Domain\Contact\ValueObjects;

enum ContactStatus: string
{
    case Pending    = 'pending';
    case Processing = 'processing';
    case Active     = 'active';
    case Failed     = 'failed';
}
