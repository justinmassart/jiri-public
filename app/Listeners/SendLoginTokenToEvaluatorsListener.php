<?php

namespace App\Listeners;

use App\Events\SendLoginTokenToEvaluatorsEvent;
use App\Jobs\SendLoginTokenToEvaluatorsJob;

class SendLoginTokenToEvaluatorsListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
    }

    /**
     * Handle the event.
     */
    public function handle(SendLoginTokenToEvaluatorsEvent $event): void
    {
        SendLoginTokenToEvaluatorsJob::dispatch($event->accessToken, $event->accessToken->contact);
    }
}
