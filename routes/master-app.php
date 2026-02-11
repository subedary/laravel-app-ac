<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MasterApp\RoleController;
use App\Http\Controllers\MasterApp\UserController;
use App\Http\Controllers\MasterApp\EntityInfoController;
use App\Http\Controllers\MasterApp\ModuleController;
use App\Http\Controllers\MasterApp\PermissionController;
use App\Http\Controllers\MasterApp\TimeOffRequestController;
use App\Http\Controllers\MasterApp\DashboardController;
use App\Http\Controllers\MasterApp\TimesheetController;
use App\Http\Controllers\MasterApp\TimesheetClockController;
use App\Http\Controllers\MasterApp\UserTimesheetController;
use App\Http\Controllers\MasterApp\DriverController;
use App\Http\Controllers\MasterApp\NotificationController;
use App\Http\Controllers\MasterApp\WordpressController;
use App\Http\Controllers\MasterApp\SettingsController;
use App\Http\Controllers\MasterApp\ContactController;

// Dashboard
Route::middleware(['auth', 'verified'])->get('/dashboard', function () {
    return view('masterapp.dashboard');
})->name('dashboard');

// Protected Routes
Route::middleware('auth')->group(function () {

Route::prefix('master-app')
    // ->middleware(['auth'])
    ->name('masterapp.')
    ->group(function () 
    {
        Route::get('/dashboard', [DashboardController::class, 'index'])
            ->name('dashboard');
        Route::get('/users', [UserController::class, 'index'])
            ->name('users.index');
        Route::get('/users/create', [UserController::class, 'create'])
            ->name('users.create');

        Route::post('/users/store', [UserController::class, 'store'])
            ->name('users.store');

        Route::get('/users/{id}/edit', [UserController::class, 'edit'])
            ->name('users.edit');

        Route::put('/users/{id}', [UserController::class, 'update'])
            ->name('users.update');

        Route::delete('/users/{id}', [UserController::class, 'destroy'])
            ->name('users.destroy');

         //  ajax toggle without page reload
        Route::patch('/users/{id}/toggle-active', [UserController::class, 'toggleActive'])->name('users.toggle-active');
        Route::patch('/users/{id}/status', [UserController::class, 'updateStatus'])->name('users.updateStatus');
        Route::get('/entity/{type}/{id}', [EntityInfoController::class, 'show'])->name('entity.info');
        Route::get('/entity/{type}/{id}/tab/{tab}', [EntityInfoController::class, 'showTab'])->name('entity.info.tab');
        Route::get('/entity/{type}/{id}/modal/{modal}', [EntityInfoController::class, 'showModal'])->name('entity.info.modal');
        // Route::resource('users', UserController::class);

        Route::patch('users/{id}/password', [UserController::class, 'updatePassword'])->name('users.password.update');

           Route::resource('users', UserController::class)->except(['create', 'edit'])
           ->names([
               'index'   => 'users.index',
               'store'   => 'users.store',
               'show'    => 'users.show',
               'update'  => 'users.update',
               'destroy' => 'users.destroy',
           ])
           ->middleware([
               'index'   => 'can:list-users',
               'store'   => 'can:create-user',
               'show'    => 'can:list-users',
               'update'  => 'can:edit-user',
               'destroy' => 'can:delete-user',
           ]);

       // Additional custom routes for users

        Route::prefix('users')->name('users.')->group(function () {
           Route::get('/create', [UserController::class, 'create'])->name('create')->middleware('can:create-user');
           Route::get('/{id}/edit', [UserController::class, 'edit'])->name('edit')->middleware('can:edit-user');
           Route::patch('/{id}/toggle-active', [UserController::class, 'toggleActive'])->name('toggle-active')->middleware('can:edit-user');
           Route::patch('/{id}/status', [UserController::class, 'updateStatus'])->name('updateStatus')->middleware('can:edit-user');
           Route::patch('/{id}/password', [UserController::class, 'updatePassword'])->name('password.update')->middleware('can:edit-user');
        });

        // TIME OFF REQUESTS

        Route::prefix('time-off-requests')->name('time-off-requests.')->group(function () {
            Route::get('/data', [TimeOffRequestController::class, 'data'])->name('data');
            Route::get('/', [TimeOffRequestController::class, 'index'])->name('index');
            Route::post('/store', [TimeOffRequestController::class, 'store'])->name('store');
            Route::put('/{id}', [TimeOffRequestController::class, 'update'])->name('update');
            Route::patch('/{id}/status', [TimeOffRequestController::class, 'updateStatus'])->name('updateStatus');
            Route::post('/bulk-status', [TimeOffRequestController::class, 'bulkUpdateStatus'])->name('bulkUpdateStatus');
            Route::delete('/{id}', [TimeOffRequestController::class, 'destroy'])->name('destroy');
            Route::get('/export', [TimeOffRequestController::class, 'export'])->name('export');

        });

        /*  DRIVERS  */
        Route::get('/drivers', [DriverController::class, 'index'])->name('drivers.index');
        //  ajax toggle without page reload
        Route::patch('/drivers/{id}/toggle-active', [DriverController::class, 'toggleActive'])->name('drivers.toggle-active');
        Route::patch('/drivers/{id}/status', [DriverController::class, 'updateStatus'])->name('drivers.updateStatus');

        // Wordpress Users
        Route::prefix('wordpress')->name('wordpress.')->group(function () {
            Route::get('/', [WordpressController::class, 'index'])->name('index');
            Route::patch('/{id}/toggle-active', [WordpressController::class, 'toggleActive'])->name('toggle-active');
        });

        /* CONTACTS */
        Route::get('/contacts', [ContactController::class, 'index'])->name('contacts.index');
        Route::get('/contacts/data', [ContactController::class, 'getContacts'])->name('contacts.data');
        Route::get('/contacts/create', [ContactController::class, 'create'])->name('contacts.create');
        Route::post('/contacts', [ContactController::class, 'store'])->name('contacts.store');
        Route::get('/contacts/{id}', [ContactController::class, 'show'])->name('contacts.show');
        Route::get('/contacts/{id}/items', [ContactController::class, 'getContactsitems'])->name('contacts.items');
        Route::get('/contacts/{id}/items/summary', [ContactController::class, 'getContactItemsSummary'])->name('contacts.items.summary');
        Route::get('/contacts/{id}/edit', [ContactController::class, 'edit'])->name('contacts.edit');
        Route::put('/contacts/{id}', [ContactController::class, 'update'])->name('contacts.update');
        Route::delete('/contacts/{id}', [ContactController::class, 'destroy'])->name('contacts.destroy');

        Route::get('/contacts/{id}/items/create', [ContactController::class, 'createContactItem'])->name('contact-items.create');
        Route::post('/contacts/{id}/items', [ContactController::class, 'storeContactItem'])->name('contact-items.store');
        Route::get('/contact-items/{id}/edit', [ContactController::class, 'editContactItem'])->name('contact-items.edit');
        Route::put('/contact-items/{id}', [ContactController::class, 'updateContactItem'])->name('contact-items.update');
        Route::delete('/contact-items/{id}', [ContactController::class, 'destroyContactItem'])->name('contact-items.destroy');



         /*  NOTIFICATIONS  */
        Route::get('notifications', [NotificationController::class, 'index'])
            ->name('notifications.index');

        Route::match(['get', 'patch'], 'notifications/{id}/read', [NotificationController::class, 'markAsRead'])
            ->name('notifications.read');

        Route::patch('notifications/read-all', [NotificationController::class, 'markAllRead'])
            ->name('notifications.read-all');

         /*  MODULES   */
        Route::resource('modules', ModuleController::class)->middleware([
                'index' => 'can:list-modules',
                 'create' => 'can:create-modules',
                 'edit'   => 'can:edit-modules',
                'destroy'=> 'can:delete-modules',
            ]);



         /*  PERMISSIONS  */

        Route::resource('permissions', PermissionController::class)->middleware([
                'index' => 'can:list-permission',
                 'create' => 'can:create-permission',
                 'edit'   => 'can:edit-permission',
                'destroy'=> 'can:delete-permission',
            ]);

         /*   ROLES   */
        Route::get('/roles/data', [RoleController::class, 'getRoles'])
                ->name('roles.data')->middleware('can:list-role');

            // Route::resource('roles', RoleController::class)->middleware([
            //     'index' => 'can:list-role',
            //      'create' => 'can:create-role',
            //      'edit'   => 'can:edit-role',
            //     'destroy'=> 'can:delete-role',
            // ]);

            Route::resource('roles', RoleController::class);

            // Route::resource('roles', RoleController::class)
            //     ->middleware('can:list-role', ['only' => ['index']])           // View list
            //     ->middleware('can:create-role', ['only' => ['create', 'store']])   // Create
            //     ->middleware('can:edit-role', ['only' => ['edit', 'update']])       // Edit
            //     ->middleware('can:delete-role', ['only' => ['destroy']]);         // Delete

            // Separate route for bulk delete, as it's not a standard resource action
            // Route::post('/roles/bulk-delete', [RoleController::class, 'bulkDestroy'])
            //     ->name('roles.bulk-delete')
            //     ->middleware('can:delete-role');


        // Route::resource('permissions', PermissionController::class)
        // ->middleware('can:list-permission', ['only' => ['index']])           // View list
        // ->middleware('can:create-permission', ['only' => ['create', 'store']])   // Create
        // ->middleware('can:edit-permission', ['only' => ['edit', 'update']])       // Edit
        // ->middleware('can:delete-permission', ['only' => ['destroy']]);         // Delete



        // Route::resource('modules', ModuleController::class);



    Route::get('test-email', [App\Http\Controllers\MasterApp\TestemailController::class, 'index'])->name('testemail.index');


        // SETTINGS PAGE
        Route::get('/settings', [SettingsController::class, 'index'])->name('settings');

        // TIMESHEETS
        Route::prefix('timesheets')->name('timesheets.')->group(function () {

            // DataTables AJAX
            Route::get('/data', [TimesheetController::class, 'getTimesheets'])
                ->name('data')->middleware('can:list-timesheets');

            // Timesheet Standard resource routes
            Route::get('/', [TimesheetController::class,'index'])->name('index')->middleware('can:list-timesheets');
            Route::post('/', [TimesheetController::class,'store'])->name('store')->middleware('can:create-timesheet');
            Route::get('/{timesheet}', [TimesheetController::class,'show'])->name('show')->middleware('can:list-timesheets');
            Route::get('/{timesheet}/edit', [TimesheetController::class,'edit'])->name('edit')->middleware('can:edit-timesheet');
            Route::get('/{timesheet}/json', [TimesheetController::class,'json'])->name('json')->middleware('can:edit-timesheet');
            Route::put('/{timesheet}', [TimesheetController::class,'update'])->name('update')->middleware('can:edit-timesheet');
            Route::delete('/{timesheet}', [TimesheetController::class,'destroy'])->name('destroy')->middleware('can:delete-timesheet');
        });


    // CLOCK IN / OUT

    Route::prefix('dashboard')->name('dashboard.')->group(function () {
        Route::post('/clock-in', [TimesheetClockController::class, 'clockIn'])->name('clock-in');

            Route::post('/clock-out', [TimesheetClockController::class, 'clockOut'])->name('clock-out');
        });

        // USER TIMESHEET (CALENDAR)
        Route::prefix('users/{user}/timesheets')->name('users.timesheets.')->group(function () {

            Route::get('/', [UserTimesheetController::class, 'index'])->name('index');
            Route::get('/calendar', [UserTimesheetController::class, 'calendarEvents'])->name('calendar');
        });
    });

  });
