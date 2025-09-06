<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Http\Controllers\Api\Enrollee\EnrolleeController;
use App\Repo\Enrollee\EnrolleeInterface;
use App\Repo\Enrollee\EnrolleeRepository;
class EnrolleeServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
         $this->app->when(EnrolleeController::class)
        ->needs(EnrolleeInterface::class)
        ->give(EnrolleeRepository::class);
    }
}
