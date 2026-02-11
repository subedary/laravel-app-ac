<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use OwenIt\Auditing\Models\Audit;
use OwenIt\Auditing\Policies\AuditPolicy;
use App\Models\User;
use App\Models\Module;
use App\Policies\ModulePolicy;
use App\Models\Timesheet;
use App\Policies\TimesheetPolicy;
use App\Policies\NotificationPolicy;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Audit::class => AuditPolicy::class,
         Module::class => ModulePolicy::class,
         Timesheet::class => TimesheetPolicy::class,
         DatabaseNotification::class => NotificationPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot()
    {
        $this->registerPolicies();

        Gate::define('view-audits', function (User $user) {
            // If you use is_admin column
            return $user->is_admin == 1;

            // OR, if using Spatie roles:
            // return $user->hasRole('super-admin');
        });

        // Notification Gates
        Gate::define('view-notifications', [NotificationPolicy::class, 'view']);
        Gate::define('mark-notification-read', [NotificationPolicy::class, 'markAsRead']);
        Gate::define('mark-all-notifications-read', [NotificationPolicy::class, 'markAllAsRead']);

    }
}
