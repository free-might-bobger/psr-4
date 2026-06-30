<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        'App\Model\Announcement' => 'App\Policies\AnnouncementPolicy',
        'App\Model\Category' => 'App\Policies\CategoryPolicy',
        'App\Model\Role' => 'App\Policies\RolePolicy',
        'App\Models\User' => 'App\Policies\UserPolicy',
        'App\Model\Product' => 'App\Policies\ProductPolicy',
        'App\Model\Branch' => 'App\Policies\BranchPolicy',
        'App\Model\Payment' => 'App\Policies\PaymentPolicy',
        'App\Model\Balance' => 'App\Policies\BalancePolicy',
        'App\Model\ConfirmEnrolled' => 'App\Policies\ConfirmEnrolledPolicy',
        'App\Model\Curriculum' => 'App\Policies\CurriculumPolicy',
        'App\Model\SHSModuleFolder' => 'App\Policies\SHSModulePolicy',
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();
        Gate::resource('users', 'App\Policies\UserPolicy');
        Gate::resource('dashboard_announcements', 'App\Policies\AnnouncementPolicy');
        Gate::resource('dashboard_products', 'App\Policies\ProductPolicy');
        Gate::resource('dashboard_role', 'App\Policies\RolePolicy');
        Gate::resource('dashboard_categories', 'App\Policies\CategoryPolicy');
        Gate::resource('branches', 'App\Policies\BranchPolicy');
        Gate::resource('payments', 'App\Policies\PaymentPolicy');
        Gate::resource('balances', 'App\Policies\BalancePolicy');
        Gate::resource('confirm_enrolled', 'App\Policies\ConfirmEnrolledPolicy');
        Gate::resource('curriculums', 'App\Policies\CurriculumPolicy');
        Gate::resource('shs_module_folders', 'App\Policies\SHSModulePolicy');
    }
}
