<?php

namespace App\Infrastructure\Contact\Listeners;

use App\Domain\Contact\Events\ContactScoreProcessed;
use Illuminate\Support\Facades\Log;

class LogContactScoreListener
{
    public function handle(ContactScoreProcessed $event): void
    {
        Log::channel('contact')->info('Contact score processed', [
            'id'     => $event->contactId,
            'email'  => $event->email,
            'score'  => $event->score,
            'status' => $event->status,
        ]);
    }
}
