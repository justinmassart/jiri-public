<?php

namespace App\Providers;

use App\Events\RecoveredPasswordEvent;
use App\Events\SendLoginTokenToEvaluatorsEvent;
use App\Listeners\RecoveredPasswordListener;
use App\Listeners\SendLoginTokenToEvaluatorsListener;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        RecoveredPasswordEvent::class => [
            RecoveredPasswordListener::class,
        ],
        SendLoginTokenToEvaluatorsEvent::class => [
            SendLoginTokenToEvaluatorsListener::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //Event::listen(NoteCreatedEvent::class, [NoteCreatedListener::class, 'handle']);

        //Event::listen(RecoveredPasswordEvent::class, [RecoveredPasswordListener::class, 'handle']);
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
