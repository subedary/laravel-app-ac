<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;


class EnsureCanViewAudits
{
    /**
     * If you prefer to use role/permission checks (spatie/laravel-permission),
     * replace the Gate check with $request->user()->hasPermissionTo('view audits') or ->hasRole('admin')
     */
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        // Example using a Gate named 'view-audits'
        if ($user && Gate::allows('view-audits')) {
            return $next($request);
        }

        // Example fallback: allow only users with is_admin boolean
        if ($user && ($user->is_admin ?? false)) {
            return $next($request);
        }

        abort(403, 'Unauthorized to view audits');
    }
}
