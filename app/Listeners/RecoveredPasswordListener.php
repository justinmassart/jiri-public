<?php

namespace App\Listeners;

use App\Events\RecoveredPasswordEvent;
use App\Jobs\SendRecoveredPasswordMailJob;

class RecoveredPasswordListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(RecoveredPasswordEvent $event): void
    {
        SendRecoveredPasswordMailJob::dispatch($event->password, $event->password->user);
    }
}
