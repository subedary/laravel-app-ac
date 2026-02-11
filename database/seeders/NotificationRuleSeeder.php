<?php

namespace Database\Seeders;

use App\Models\NotificationRule;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class NotificationRuleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = Role::pluck('name')->toArray();
        $adminRole = 'Admin User';

        // Safety: stop if role does not exist
        if (!in_array($adminRole, $roles)) {
            return;
        }

        $rules = [
            // User CRUD
            ['event_key' => 'user.created'],
            ['event_key' => 'user.updated'],
            ['event_key' => 'user.deleted'],

            // Role
            ['event_key' => 'role.updated'],

            // Timesheet
            ['event_key' => 'timesheet.created'],
            ['event_key' => 'timesheet.updated'],
            ['event_key' => 'timesheet.deleted'],

            // Time off Requests
            ['event_key' => 'timeoff.created'],
            ['event_key' => 'timeoff.updated'],
            ['event_key' => 'timeoff.deleted'],
        ];

        foreach ($rules as $rule) {
            NotificationRule::updateOrCreate(
                [
                    'event_key' => $rule['event_key'],
                    'role_name' => $adminRole,
                ],
                [
                    'notify_creator' => false,
                    'channels' => ['database'],
                ]
            );
        }

        // Notify requestor (no role needed)
        foreach (['timeoff.approved', 'timeoff.rejected'] as $eventKey) {
            NotificationRule::updateOrCreate(
                [
                    'event_key' => $eventKey,
                    'role_name' => null,
                    'permission_name' => null,
                ],
                [
                    'notify_creator' => false,
                    'channels' => ['database'],
                ]
            );
        }
    }
}
