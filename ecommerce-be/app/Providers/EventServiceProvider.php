<?php

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;
use App\Models\Transaction;
use App\Observers\TransactionObserver;
use App\Models\User;
use App\Models\StoreUser;
use App\Models\RoleUser;
use App\Observers\UserObserver;
use App\Observers\StoreUserObserver;
use App\Observers\RoleUserObserver;
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
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        Transaction::observe(TransactionObserver::class);
        User::observe(UserObserver::class);
        StoreUser::observe(StoreUserObserver::class);
        RoleUser::observe(RoleUserObserver::class);
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
